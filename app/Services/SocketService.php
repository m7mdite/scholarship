<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;

class SocketService
{
    protected string $url;

    public function __construct()
    {
        $this->url = config('services.socket.url', 'http://localhost:4000');
    }

    public function emit(string $event, array $data, ?string $room = null): void
    {
       $response = Http::post("{$this->url}/emit", [
            'event' => $event,
            'room'  => $room,
            'data'  => $data,
        ]);
        Log::info('Socket emit response', [
        'success' => $response->successful(),
        'status'  => $response->status(),
        'body'    => $response->body(),
    ]);
    }

    public function emitToMany(string $event, array $data, array $rooms): void
    {
        Http::post("{$this->url}/emit", [
            'event' => $event,
            'rooms' => $rooms,
            'data'  => $data,
        ]);
    }
}