import dotenv from 'dotenv';
import { createServer } from 'http';
import { Server } from 'socket.io';
import Redis from 'ioredis';
import jwt from 'jsonwebtoken';
import axios from 'axios';

dotenv.config({ path: '../.env' });

const port = Number(process.env.REALTIME_PORT || 3001);
const redisUrl = process.env.REALTIME_REDIS_URL || 'redis://redis:6379';
const jwtSecret = process.env.REALTIME_JWT_SECRET;
const ciBase = process.env.REALTIME_CI_API_BASE || 'http://nginx';
const sharedSecret = process.env.REALTIME_SHARED_SECRET;
const chatLimit = Number(process.env.REALTIME_CHAT_LIMIT || 5);
const chatWindowSeconds = Number(process.env.REALTIME_CHAT_WINDOW_SECONDS || 10);

if (!jwtSecret || !sharedSecret) {
  throw new Error('Missing REALTIME_JWT_SECRET or REALTIME_SHARED_SECRET');
}

const redis = new Redis(redisUrl);
const httpServer = createServer();

const io = new Server(httpServer, {
  cors: {
    origin: ['http://localhost:8080'],
    credentials: true,
  },
});

const roomPresenceKey = (roomId) => `pixelhavn:presence:${roomId}`;
const chatRateKey = (userId) => `pixelhavn:chat_rate:${userId}`;

const authMiddleware = (socket, next) => {
  try {
    const token = socket.handshake.auth?.token;
    if (!token) return next(new Error('Missing token'));

    const payload = jwt.verify(token, jwtSecret, { algorithms: ['HS256'], audience: 'pixelhavn-realtime' });
    socket.user = {
      id: Number(payload.sub),
      username: payload.username,
      roles: payload.roles || [],
      roomId: Number(payload.room_id),
    };
    return next();
  } catch (err) {
    return next(new Error('Invalid auth token'));
  }
};

io.use(authMiddleware);

io.on('connection', (socket) => {
  socket.on('joinRoom', async ({ roomId }) => {
    if (Number(roomId) !== socket.user.roomId) return;

    const room = `room:${roomId}`;
    socket.join(room);
    await redis.hset(roomPresenceKey(roomId), socket.id, JSON.stringify({ id: socket.user.id, username: socket.user.username }));

    const users = await getPresenceUsers(roomId);
    io.to(room).emit('presenceUpdate', { roomId: Number(roomId), users });
  });

  socket.on('leaveRoom', async ({ roomId }) => {
    await removePresence(socket, Number(roomId));
    socket.leave(`room:${roomId}`);
  });

  socket.on('chatMessage', async ({ roomId, message }) => {
    if (Number(roomId) !== socket.user.roomId) return;
    if (typeof message !== 'string') return;
    const text = message.trim().slice(0, 500);
    if (!text) return;

    const allowed = await enforceChatRateLimit(socket.user.id);
    if (!allowed) {
      socket.emit('errorMessage', { error: 'Rate limit exceeded' });
      return;
    }

    const payload = {
      event: 'chatMessage',
      room_id: Number(roomId),
      user_id: socket.user.id,
      message: text,
    };

    await axios.post(`${ciBase}/api/realtime/event`, new URLSearchParams(payload), {
      headers: { 'X-Realtime-Secret': sharedSecret },
      timeout: 5000,
    });

    io.to(`room:${roomId}`).emit('chatMessage', {
      roomId: Number(roomId),
      userId: socket.user.id,
      username: socket.user.username,
      message: text,
      createdAt: new Date().toISOString(),
    });
  });

  socket.on('move', ({ roomId, x, y }) => {
    if (Number(roomId) !== socket.user.roomId) return;
    io.to(`room:${roomId}`).emit('move', {
      roomId: Number(roomId),
      userId: socket.user.id,
      username: socket.user.username,
      x: Number(x) || 0,
      y: Number(y) || 0,
    });
  });

  socket.on('placeItem', (payload) => {
    const roomId = Number(payload.roomId);
    if (roomId !== socket.user.roomId) return;
    io.to(`room:${roomId}`).emit('placeItem', {
      roomId,
      userId: socket.user.id,
      username: socket.user.username,
      itemId: Number(payload.itemId),
      itemName: payload.itemName || 'Item',
      x: Number(payload.x) || 0,
      y: Number(payload.y) || 0,
      rotation: Number(payload.rotation) || 0,
    });
  });

  socket.on('disconnect', async () => {
    const roomId = socket.user.roomId;
    await removePresence(socket, roomId);
  });
});

async function enforceChatRateLimit(userId) {
  const key = chatRateKey(userId);
  const count = await redis.incr(key);
  if (count === 1) {
    await redis.expire(key, chatWindowSeconds);
  }
  return count <= chatLimit;
}

async function getPresenceUsers(roomId) {
  const raw = await redis.hvals(roomPresenceKey(roomId));
  return raw.map((entry) => JSON.parse(entry));
}

async function removePresence(socket, roomId) {
  await redis.hdel(roomPresenceKey(roomId), socket.id);
  const users = await getPresenceUsers(roomId);
  io.to(`room:${roomId}`).emit('presenceUpdate', { roomId, users });
}

httpServer.listen(port, () => {
  console.log(`PixelHavn realtime listening on ${port}`);
});
