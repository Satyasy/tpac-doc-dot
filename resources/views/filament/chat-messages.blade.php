@php
    use App\Models\ChatMessage;
@endphp

<div class="space-y-4">
    @forelse($messages as $message)
        <div
            class="p-4 rounded-lg {{ $message->sender === 'user' ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-green-50 dark:bg-green-900/20' }}">
            <div class="flex items-center gap-2 mb-2">
                <span
                    class="px-2 py-1 text-xs font-medium rounded {{ $message->sender === 'user' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' }}">
                    {{ ucfirst($message->sender) }}
                </span>
                <span class="text-xs text-gray-500">
                    {{ $message->created_at->format('Y-m-d H:i:s') }}
                </span>
            </div>
            <div class="prose dark:prose-invert max-w-none">
                {!! nl2br(e($message->message)) !!}
            </div>
        </div>
    @empty
        <p class="text-gray-500">No messages in this session.</p>
    @endforelse
</div>
