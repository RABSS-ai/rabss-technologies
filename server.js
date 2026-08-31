// server.js
// Customizable WebSockets Signaling Server for custom WebRTC video sessions
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const axios = require('axios'); // For making HTTP requests to PHP API
const app = express();
app.use(cors());

const server = http.createServer(app);
const io = new Server(server, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"]
  }
});

const rooms = {}; // Store room data, including host socket ID

io.on('connection', (socket) => {
  console.log('User connected:', socket.id);

  socket.on('join-room', ({ roomId, isHost }) => {
    socket.join(roomId);
    if (isHost) {
      rooms[roomId] = { hostSocketId: socket.id };
    }
    console.log(`User ${socket.id} joined room ${roomId}`);
    socket.to(roomId).emit('user-joined', socket.id);
  });

  socket.on('offer', ({ offer, to }) => {
    io.to(to).emit('offer', { offer, from: socket.id });
  });

  socket.on('answer', ({ answer, to }) => {
    io.to(to).emit('answer', { answer, from: socket.id });
  });

  socket.on('ice-candidate', ({ candidate, to }) => {
    io.to(to).emit('ice-candidate', { candidate, from: socket.id });
  });

  socket.on('chat-message', ({ roomId, message, sender }) => {
    socket.to(roomId).emit('chat-message', { message, sender });
  });

  socket.on('toggle-voice-rec', ({ roomId, enabled }) => {
    socket.to(roomId).emit('remote-toggle-voice-rec', { enabled });
  });

  socket.on('voice-query', async ({ roomId, query, sender }) => {
    console.log(`Voice query from ${sender} in room ${roomId}: ${query}`);
    const roomInfo = rooms[roomId];
    if (roomInfo && roomInfo.hostSocketId) {
      try {
        // Make HTTP request to PHP AI API
        const phpApiUrl = `http://localhost/admin/api/index.php?action=ask_ai`; // Adjust URL as needed
        const response = await axios.post(phpApiUrl, { query: query }, {
          headers: {
            'Content-Type': 'application/json'
          }
        });

        if (response.data && response.data.status === 'success') {
          io.to(roomInfo.hostSocketId).emit('ai-suggestion', { suggestion: response.data.answer });
          console.log(`AI suggestion sent to host ${roomInfo.hostSocketId}: ${response.data.answer}`);
        }
      } catch (error) {
        console.error('Error calling PHP AI API:', error.message);
        io.to(roomInfo.hostSocketId).emit('ai-suggestion', { suggestion: `Error processing voice query: ${error.message}` });
      }
    }
  });

  socket.on('disconnect', () => {
    console.log('User disconnected:', socket.id);
    io.emit('user-left', socket.id);
  });
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
  console.log(`RABSS OS Signaling Server is running on port ${PORT}`);
});