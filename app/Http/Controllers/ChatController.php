<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Services\Rag\RagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function __construct(
        private RagService $ragService
    ) {
    }

    /**
     * Show consultation page
     */
    public function index()
    {
        $sessions = [];
        $user = Auth::user();
        $userRole = 'patient';

        if ($user) {
            $sessions = ChatSession::where('user_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->get();

            // Determine user role for chatbot
            if ($user->hasRole('doctor')) {
                $userRole = 'doctor';
            }
        }

        return Inertia::render('Consultation', [
            'sessions' => $sessions,
            'userRole' => $userRole,
        ]);
    }

    /**
     * Create new chat session
     */
    public function createSession(Request $request)
    {
        $session = ChatSession::create([
            'user_id' => Auth::id(),
            'title' => 'Konsultasi Baru',
        ]);

        return response()->json([
            'success' => true,
            'session' => $session,
        ]);
    }

    /**
     * Get messages for a session
     */
    public function getMessages(ChatSession $session)
    {
        // Ensure user owns this session
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $messages = $session->messages()->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message and get AI response
     */
    public function sendMessage(Request $request, ChatSession $session)
    {
        // Ensure user owns this session
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        $userRole = $user->hasRole('doctor') ? 'doctor' : 'patient';
        $userName = $user->name;

        // Save user message
        $userMessage = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender' => 'user',
            'message' => $request->message,
        ]);

        // Update session title if first message
        if ($session->messages()->count() === 1) {
            $session->update([
                'title' => substr($request->message, 0, 50) . (strlen($request->message) > 50 ? '...' : ''),
            ]);
        }

        try {
            // Get AI response using RAG with role info
            $result = $this->ragService->query(
                $request->message,
                5,
                null,
                $userRole,
                $userName
            );

            // Save AI response
            $aiMessage = ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender' => 'ai',
                'message' => $result['answer'],
                'structured_response' => [
                    'sources' => $result['sources'] ?? [],
                    'context_count' => $result['context_count'] ?? 0,
                    'intent' => $result['intent'] ?? 'health',
                ],
            ]);

            return response()->json([
                'success' => true,
                'user_message' => $userMessage,
                'ai_message' => $aiMessage,
            ]);
        } catch (\Exception $e) {
            // Fallback response if RAG fails
            $aiMessage = ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender' => 'ai',
                'message' => 'Maaf, saya sedang mengalami gangguan teknis 🙏 Silakan coba lagi dalam beberapa saat. Jika masalah berlanjut, Anda bisa langsung menghubungi dokter atau fasilitas kesehatan terdekat.',
            ]);

            return response()->json([
                'success' => true,
                'user_message' => $userMessage,
                'ai_message' => $aiMessage,
            ]);
        }
    }

    /**
     * Delete a chat session
     */
    public function deleteSession(ChatSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Show chat history page
     */
    public function history(Request $request)
    {
        $query = ChatSession::where('user_id', Auth::id())
            ->withCount('messages');

        // Search filter
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Date filter
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $sessions = $query->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Get statistics
        $totalSessions = ChatSession::where('user_id', Auth::id())->count();
        $totalMessages = ChatMessage::whereHas('session', function ($q) {
            $q->where('user_id', Auth::id());
        })->count();
        $thisMonthSessions = ChatSession::where('user_id', Auth::id())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return Inertia::render('ChatHistory', [
            'sessions' => $sessions,
            'stats' => [
                'total_sessions' => $totalSessions,
                'total_messages' => $totalMessages,
                'this_month_sessions' => $thisMonthSessions,
            ],
            'filters' => [
                'search' => $request->search ?? '',
                'date' => $request->date ?? '',
            ],
        ]);
    }
}
