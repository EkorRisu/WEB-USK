<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // <-- Pastikan ini di-import
use App\Models\Message;
use App\Notifications\NewUserMessage;

class ChatController extends Controller
{
    /**
     * Menampilkan halaman chat dan menandai notifikasi sebagai terbaca.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // ... (Logika Admin Index Anda sudah benar) ...
            $users = User::where('role', 'user')->get();
            $selectedUser = null;
            $messages = collect();

            if ($request->has('user_id')) {
                $selectedUser = User::findOrFail($request->user_id);

                // TANDAI NOTIFIKASI DARI USER INI SEBAGAI SUDAH DIBACA
                Auth::user()->notifications()
                    ->where('type', 'App\Notifications\NewUserMessage')
                    ->whereJsonContains('data->sender_id', $selectedUser->id)
                    ->update(['read_at' => now()]);
                
                $messages = Message::where(function ($query) use ($selectedUser) {
                    $query->where('sender_id', Auth::id())
                          ->where('receiver_id', $selectedUser->id);
                })->orWhere(function ($query) use ($selectedUser) {
                    $query->where('sender_id', $selectedUser->id)
                          ->where('receiver_id', Auth::id());
                })
                    ->with('sender', 'receiver')
                    ->orderBy('created_at')
                    ->get();
            }

            return view('admin.chat', compact('users', 'selectedUser', 'messages'));
        
        } else {
            // Logika untuk User
            $admin = User::where('role', 'admin')->first();

            if (!$admin) {
                return redirect()->route('user.dashboard')->with('error', 'Admin tidak ditemukan.');
            }

            $messages = Message::where(function ($query) use ($user, $admin) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $admin->id);
            })->orWhere(function ($query) use ($user, $admin) {
                $query->where('sender_id', $admin->id)
                      ->where('receiver_id', $user->id);
            })
                ->with('sender', 'receiver')
                ->orderBy('created_at')
                ->get();

            // TANDAI NOTIFIKASI DARI ADMIN SEBAGAI SUDAH DIBACA
            Auth::user()->notifications()
                ->where('type', 'App\Notifications\NewUserMessage')
                ->whereJsonContains('data->sender_id', $admin->id)
                ->update(['read_at' => now()]);

            return view('user.chat', compact('messages'));
        }
    }

    /**
     * Menyimpan pesan baru dan mengirim notifikasi.
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        $receiver = User::find($request->receiver_id);
        $sender = Auth::user();

        if ($receiver) {
            
            // CEK 1: USER ke ADMIN (Sudah diperbaiki)
            if ($receiver->role === 'admin') {
                $receiver->notify(new NewUserMessage($message, $sender));
            } 
            // CEK 2: ADMIN ke USER
            elseif ($receiver->role === 'user') { 
                $receiver->notify(new NewUserMessage($message, $sender));
            }
        }
        
        return redirect()->back();
    }

    // --- FUNGSI BARU YANG HILANG ---
    
    /**
     * (BARU) Mengambil jumlah notifikasi admin yang belum dibaca oleh user.
     * Ini dipanggil oleh AJAX dari dashboard.
     */
    public function getUnreadCount()
    {
        $user = Auth::user();

        // Cari admin
        $admin = User::where('role', 'admin')->first();

        // Jika tidak ada admin, tidak ada notifikasi
        if (!$admin) {
            return response()->json(['count' => 0]);
        }

        // Hitung notifikasi yang belum dibaca dari admin
        $count = $user->unreadNotifications()
            ->where('type', 'App\Notifications\NewUserMessage')
            ->whereJsonContains('data->sender_id', $admin->id)
            ->count();

        return response()->json(['count' => $count]);
    }
}