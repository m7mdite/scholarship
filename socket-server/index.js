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
    cors: { origin: '*' } // حدد دومين React في الإنتاج
});

const LARAVEL_API = 'http://localhost:8000/api'; // عدّل حسب مشروعك

io.on('connection', (socket) => {
    console.log('Client connected:', socket.id);

    // العميل يرسل التوكن عند الاتصال، ونحن نتحقق منه ونجيب هوية المستخدم الحقيقية
    socket.on('authenticate', async (token) => {
        try {
            const res = await axios.get(`${LARAVEL_API}/user`, {
                headers: { Authorization: `Bearer ${token}` }
            });

            const userId = res.data.id;
            socket.userId = userId;
            socket.join(`user_${userId}`); // غرفة خاصة بالمستخدم

            socket.emit('authenticated', { success: true, userId });
            console.log(`User ${userId} joined room user_${userId}`);
        } catch (err) {
            console.error('فشل التحقق من التوكن:', err.response?.status, err.response?.data || err.message);
            socket.emit('authenticated', { success: false });
            socket.disconnect(); // توكن غير صالح -> افصله
        }
    });

    socket.on('disconnect', () => {
        console.log('Client disconnected:', socket.id, socket.userId ?? '');
    });
});

// Endpoint يستقبله لارافيل ليبثّ إشعار لمستخدم معين أو لعدة مستخدمين أو للجميع
app.post('/emit', (req, res) => {
    console.log('📤 طلب emit وصل:', { event, room, rooms });
    const { event, data, room, rooms } = req.body;
    if (rooms && Array.isArray(rooms)) {
        // إرسال لعدة غرف دفعة واحدة (مفيد لـ sendToAll)
        rooms.forEach(r => io.to(r).emit(event, data));
    } else if (room) {
        io.to(room).emit(event, data);
    } else {
        io.emit(event, data); // بث للجميع بدون استثناء
    }

    res.json({ status: 'ok' });
});

server.listen(4000, () => console.log('Socket.io server running on port 4000'));