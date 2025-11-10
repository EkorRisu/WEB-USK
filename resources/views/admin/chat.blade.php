@extends('layouts.admin')

@section('content')
    <div id="chatContainer" class="max-w-7xl mx-auto mt-10 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="col-span-1 user-list-card p-4 rounded shadow">
            <h2 class="text-lg font-bold mb-2 user-list-title">User</h2>
            <ul>
                @foreach ($users as $u)
                    <li class="mb-2">
                        <a href="{{ route('user.chat', ['user_id' => $u->id]) }}" class="user-link hover:underline">
                            {{ $u->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-span-3 chat-card p-4 rounded shadow h-[500px] overflow-y-auto">
            @if ($selectedUser)
                <h2 class="text-xl font-bold mb-4 chat-title">Chat dengan {{ $selectedUser->name }}</h2>

                <div class="space-y-2 mb-4">
                    @forelse ($messages as $msg)
                        <div class="{{ $msg->sender_id == auth()->id() ? 'text-right' : 'text-left' }}">
                            <div
                                class="inline-block px-4 py-2 rounded-lg {{ $msg->sender_id == auth()->id() ? 'message-sent' : 'message-received' }}">
                                <p class="text-sm">{{ $msg->message }}</p>
                                <p class="text-xs message-time">{{ $msg->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="empty-text">Belum ada pesan.</p>
                    @endforelse
                </div>

                <form action="{{ route('user.chat.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $selectedUser->id }}">
                    <input type="text" name="message" class="w-full border rounded p-2 chat-input" placeholder="Ketik pesan...">
                    <button type="submit" class="send-button px-4 rounded">Kirim</button>
                </form>
            @else
                <p class="empty-text">Pilih user untuk memulai percakapan.</p>
            @endif
        </div>
    </div>

    <style>
        :root {
            --bg-gradient-start: #18181b;
            --bg-gradient-end: #27272a;
            --card-bg: rgba(30, 30, 40, 0.85);
            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --border-color: rgba(63, 63, 70, 0.5);
            --user-list-bg: rgba(30, 30, 40, 0.85);
            --user-link-color: #60a5fa;
            --user-link-hover: #93c5fd;
            --chat-bg: rgba(30, 30, 40, 0.85);
            --message-sent-bg: #1e40af;
            --message-received-bg: #374151;
            --message-text: #ffffff;
            --message-time: #9ca3af;
            --input-bg: rgba(39, 39, 42, 0.9);
            --input-border: rgba(63, 63, 70, 0.5);
            --input-focus: #3b82f6;
            --button-bg: #2563eb;
            --button-hover: #1d4ed8;
            --empty-text: #71717a;
        }

        #chatContainer[data-theme="light"] {
            --bg-gradient-start: #e0f2fe;
            --bg-gradient-end: #bae6fd;
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --border-color: rgba(203, 213, 225, 0.8);
            --user-list-bg: rgba(255, 255, 255, 0.95);
            --user-link-color: #1e40af;
            --user-link-hover: #1e3a8a;
            --chat-bg: rgba(255, 255, 255, 0.95);
            --message-sent-bg: #3b82f6;
            --message-received-bg: #e5e7eb;
            --message-text-sent: #ffffff;
            --message-text-received: #1f2937;
            --message-time: #6b7280;
            --input-bg: rgba(248, 250, 252, 0.95);
            --input-border: rgba(203, 213, 225, 0.8);
            --input-focus: #2563eb;
            --button-bg: #3b82f6;
            --button-hover: #2563eb;
            --empty-text: #64748b;
        }

        #chatContainer {
            transition: all 0.3s ease;
        }

        #chatContainer .user-list-card {
            background: var(--user-list-bg);
            color: var(--text-primary);
            border-color: var(--border-color);
            transition: all 0.3s ease;
        }

        #chatContainer .user-list-title {
            color: var(--text-primary);
            transition: color 0.3s ease;
        }

        #chatContainer .user-link {
            color: var(--user-link-color);
            transition: color 0.3s ease;
        }

        #chatContainer .user-link:hover {
            color: var(--user-link-hover);
        }

        #chatContainer .chat-card {
            background: var(--chat-bg);
            color: var(--text-primary);
            border-color: var(--border-color);
            transition: all 0.3s ease;
        }

        #chatContainer .chat-title {
            color: var(--text-primary);
            transition: color 0.3s ease;
        }

        #chatContainer .message-sent {
            background: var(--message-sent-bg);
            color: var(--message-text);
            transition: all 0.3s ease;
        }

        #chatContainer .message-received {
            background: var(--message-received-bg);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        #chatContainer[data-theme="light"] .message-received {
            color: var(--message-text-received);
        }

        #chatContainer .message-time {
            color: var(--message-time);
            transition: color 0.3s ease;
        }

        #chatContainer .chat-input {
            background: var(--input-bg);
            color: var(--text-primary);
            border-color: var(--input-border);
            transition: all 0.3s ease;
        }

        #chatContainer .chat-input:focus {
            outline: none;
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        #chatContainer .send-button {
            background: var(--button-bg);
            color: white;
            transition: all 0.3s ease;
        }

        #chatContainer .send-button:hover {
            background: var(--button-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        #chatContainer .empty-text {
            color: var(--empty-text);
            transition: color 0.3s ease;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatContainer = document.getElementById('chatContainer');
            
            // Sync theme with navbar
            const syncTheme = () => {
                const isDark = document.documentElement.classList.contains('dark');
                chatContainer.setAttribute('data-theme', isDark ? 'dark' : 'light');
            };

            // Initial sync
            syncTheme();

            // Watch for theme changes from navbar
            const observer = new MutationObserver(syncTheme);
            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        });
    </script>
@endsection