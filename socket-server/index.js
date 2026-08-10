const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const axios = require('axios');

const app = express();
app.use(cors());
app.use(express.json());

const server = http.createServer(app);
const io = new Server(server, {
    cors: { origin: '*' } 
});

const LARAVEL_API = 'http://localhost:8000/api'; 

io.on('connection', (socket) => {
    console.log('Client connected:', socket.id);

   
    socket.on('authenticate', async (token) => {
        try {
            const res = await axios.get(`${LARAVEL_API}/user`, {
               headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json' // ضيف ده كمان
            }
            });

            const userId = res.data.id;
            socket.userId = userId;
            socket.join(`user_${userId}`); 

            socket.emit('authenticated', { success: true, userId });
            console.log(`User ${userId} joined room user_${userId}`);
        } catch (err) {
            console.error('فشل التحقق من التوكن:', err.response?.status, err.response?.data || err.message);
            socket.emit('authenticated', { success: false });
            socket.disconnect(); 
        }
    });

    socket.on('disconnect', () => {
        console.log('Client disconnected:', socket.id, socket.userId ?? '');
    });
});

app.post('/emit', (req, res) => {
    const { event, data, room, rooms } = req.body;
    console.log('📤 طلب emit وصل:', { event, room, rooms });

    if (rooms && Array.isArray(rooms)) {
        rooms.forEach(r => io.to(r).emit(event, data));
    } else if (room) {
        io.to(room).emit(event, data);
    } else {
        io.emit(event, data);
    }

    res.json({ status: 'ok' });
});

server.listen(4000, () => console.log('Socket.io server running on port 4000'));