FROM node:20-alpine
WORKDIR /usr/src/app
COPY realtime/package*.json ./
RUN npm install
COPY realtime .
EXPOSE 3001
CMD ["npm", "run", "start"]
