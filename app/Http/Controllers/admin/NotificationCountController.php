<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationCountController extends Controller
{
    public function getUnreadCount()
    {
        // Hanya menghitung notifikasi yang belum dibaca milik user admin yang sedang login
        $count = auth()->user()->unreadNotifications()->where('type', 'App\Notifications\NewUserMessage')->count();
        
        return response()->json([
            'count' => $count,
        ]);
    }
}