<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\BlockedUser;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\MessageAttachment;
use App\Models\MessageLike;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatApiController extends Controller
{
    public function createChat($other_Id)
    {
        $user_id = Auth::id();
        $userOne = min($user_id, $other_Id);
        $userTwo = max($user_id, $other_Id);
        $chat = Chat::firstOrCreate(['sender_id' => $userOne, 'receiver_id' => $userTwo]);
        return response()->json(['status' => true, 'chat' => $chat]);
    }
    public function sendMessages(Request $r)
    {
        $data = $r->validate(['chat_id' => 'required|exists:chats,id', 'message' => 'nullable|string', 'attachments.*' => 'file|mimes:jpg,jpeg,png|max:10240', 'reply_to_id' => 'nullable|exists:chat_messages,id']);
        $senderId = Auth::id();
        $chat = Chat::findOrFail($data['chat_id']);
        $receiverId = ($chat->sender_id === $senderId) ? $chat->receiver_id : $chat->sender_id;

        if (BlockedUser::isBlockedBetween($senderId, $receiverId)) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot send message. User is blocked.',
            ], 403);
        }

        $message = ChatMessage::create(['chat_id' => $data['chat_id'], 'sender_id' => $senderId, 'message' => $data['message'] ?? null, 'reply_to_id' => $data['reply_to_id'] ?? null]);

        $attachments = [];

        if ($r->hasFile('attachments')) {
            foreach ($r->file('attachments') as $file) {
                $fileName = time() . '_' . Str::random(6) . '_' . $file->getClientOriginalName();
                $path = 'assets/uploads/chat/';
                if (!file_exists(public_path($path))) mkdir(public_path($path), 0777, true);
                $file->move(public_path($path), $fileName);

                $type = $this->detectFileType($file);

                $att = MessageAttachment::create([
                    'message_id' => $message->id,
                    'file_path' => $path . $fileName,
                    'type' => $type
                ]);

                $attachments[] = $att;
            }
        }
        $message->load('attachments', 'sender');

        // ── FCM HTTP v1 push notification ──────────────────────────────────
        try {
            if (!BlockedUser::isBlockedBy($receiverId, $senderId)) {
                $receiver = User::find($receiverId);

                if ($receiver && $receiver->fcm_token) {
                    $sender = Auth::user();
                    FcmService::sendPush(
                        fcmToken: $receiver->fcm_token,
                        title:    $sender->name ?? 'New Message',
                        body:     $message->message ?? '📎 Attachment',
                        data:     [
                            'type'        => 'chat',
                            'chat_id'     => (string) $data['chat_id'],
                            'sender_id'   => (string) $senderId,
                            'sender_name' => $sender->name ?? '',
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {
            // Non-fatal — log but don't fail the request
            \Illuminate\Support\Facades\Log::warning('FCM push failed in sendMessages: ' . $e->getMessage());
        }
        // ──────────────────────────────────────────────────────────────────

        return response()->json(['status' => true, 'message' => 'Message sent', 'data' => $message]);
    }
    public function getMessages($chatId)
    {
        $chat = Chat::findOrFail($chatId);
        $messages = ChatMessage::where('chat_id', $chat->id)->with(['attachments', 'likes', 'replyTo.sender', 'sender'])->orderBy('created_at', 'asc')->get();
        return response()->json(['status' => true, 'messages' => $messages]);
    }
    public function markAsRead($chatId)
    {
        ChatMessage::where('chat_id', $chatId)->where('is_read', 0)->where('sender_id', '!=', Auth::id())->update(['is_read' => 1]);
        return response()->json(['status' => true, 'message' => 'Marked as read']);
    }
    public function toggleLike(Request $r)
    {
        $data = $r->validate(['message_id' => 'required|exists:chat_messages,id', 'emoji' => 'required']);
        $userId = Auth::id();
        $like = MessageLike::where('user_id', $userId)->where('message_id', $data['message_id'])->where('emoji', $data['emoji'])->first();
        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            MessageLike::create(['user_id' => $userId, 'message_id' => $data['message_id'], 'emoji' => $data['emoji']]);
            $liked = true;
        }
        return response()->json(['status' => true, 'liked' => $liked]);
    }
    public function deleteMessage($message_Id)
    {
        $userId = Auth::id();
        $message = ChatMessage::where('id', $message_Id)->where('sender_id', $userId)->first();
        if (!$message) {
            return response()->json(['status' => false, 'message' => 'Message not found or permission denied'], 404);
        }
        $message->update(['is_deleted' => 1]);
        return response()->json(['status' => true, 'message' => 'Message deleted successfully']);
    }

    public function blockUser(Request $r)
    {
        $data = $r->validate([
            'blocked_id' => 'required|integer|exists:users,id',
        ]);

        $blockerId = Auth::id();
        $blockedId = (int) $data['blocked_id'];

        if ($blockerId === $blockedId) {
            return response()->json(['status' => false, 'message' => 'Cannot block yourself'], 400);
        }

        BlockedUser::firstOrCreate([
            'blocker_id' => $blockerId,
            'blocked_id' => $blockedId,
        ]);

        return response()->json(['status' => true, 'message' => 'User blocked successfully']);
    }

    public function unblockUser(Request $r)
    {
        $data = $r->validate([
            'blocked_id' => 'required|integer|exists:users,id',
        ]);

        BlockedUser::where('blocker_id', Auth::id())
            ->where('blocked_id', (int) $data['blocked_id'])
            ->delete();

        return response()->json(['status' => true, 'message' => 'User unblocked successfully']);
    }
    private function detectFileType($file)
    {
        $mime = $file->getClientMimeType();
        if (str_contains($mime, 'image')) {
            return 0;
        } elseif (str_contains($mime, 'video')) {
            return 1;
        } else {
            return 2;
        }
    }
}
