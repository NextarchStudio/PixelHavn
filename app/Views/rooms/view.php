<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3><?= esc($room['name']) ?></h3>
<p><?= esc($room['description']) ?></p>
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3"><div class="card-body" id="chatBox" style="height: 280px; overflow-y:auto;"></div></div>
        <div class="input-group mb-3">
            <input id="chatInput" class="form-control" placeholder="Say something..." maxlength="500">
            <button id="sendChat" class="btn btn-primary">Send</button>
        </div>
        <div class="card">
            <div class="card-body">
                <h6>Simple Position Sync</h6>
                <div class="input-group">
                    <input id="posX" class="form-control" type="number" value="0">
                    <input id="posY" class="form-control" type="number" value="0">
                    <button id="moveBtn" class="btn btn-outline-primary">Move</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3"><div class="card-body"><h6>Users In Room</h6><ul id="presenceList" class="list-group"></ul></div></div>
        <div class="card"><div class="card-body">
            <h6>Placed Items</h6>
            <ul class="list-group" id="placedItems">
                <?php foreach ($placedItems as $item): ?>
                    <li class="list-group-item"><?= esc($item['name']) ?> @ (<?= (int) $item['x'] ?>, <?= (int) $item['y'] ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div></div>
    </div>
</div>
<script>
let socket;
const roomId = <?= (int) $room['id'] ?>;
const addChat = (text) => {
    const box = document.getElementById('chatBox');
    const row = document.createElement('div');
    row.textContent = text;
    box.appendChild(row);
    box.scrollTop = box.scrollHeight;
};
const refreshPresence = (users) => {
    const list = document.getElementById('presenceList');
    list.innerHTML = '';
    users.forEach((u) => {
        const li = document.createElement('li');
        li.className = 'list-group-item';
        li.textContent = u.username;
        list.appendChild(li);
    });
};
(async function initRealtime() {
    const auth = await fetch('/api/realtime/auth', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({room_id: String(roomId)})
    });
    const authData = await auth.json();
    socket = io('<?= esc($realtimeUrl) ?>', {auth: {token: authData.token}});

    socket.emit('joinRoom', {roomId});
    socket.on('chatMessage', (msg) => addChat(`${msg.username}: ${msg.message}`));
    socket.on('presenceUpdate', (data) => refreshPresence(data.users));
    socket.on('placeItem', (data) => {
        const list = document.getElementById('placedItems');
        const li = document.createElement('li');
        li.className = 'list-group-item';
        li.textContent = `${data.itemName || 'Item'} @ (${data.x}, ${data.y})`;
        list.appendChild(li);
    });
})();

document.getElementById('sendChat').onclick = () => {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if (!message) return;
    socket.emit('chatMessage', {roomId, message});
    input.value = '';
};
document.getElementById('moveBtn').onclick = () => {
    socket.emit('move', {roomId, x: parseInt(document.getElementById('posX').value, 10), y: parseInt(document.getElementById('posY').value, 10)});
};
window.addEventListener('beforeunload', () => { if (socket) socket.emit('leaveRoom', {roomId}); });
</script>
<?= $this->endSection() ?>
