<?php

namespace App\Http\Controllers\api\site;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\ConnectionRequest;
use App\Models\MasterUserDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class ConnectionApiController extends Controller
{
    public function connectionShow()
    {
        $authId = Auth::id();
        $connections = Connection::where(function ($q) use ($authId) {
            $q->where('user_id', $authId)->orWhere('friend_id', $authId);
        })->with(['userDetail', 'friendDetail'])->get();

        return response()->json(['status' => true, 'data' => $connections]);
    }

    public function connectionCount()
    {
        $authId = Auth::id();
        $friendsCount = Connection::where(function ($q) use ($authId) {
            $q->where('user_id', $authId)->orWhere('friend_id', $authId);
        })->count();

        return response()->json(['status' => true, 'data' => $friendsCount]);
    }

    public function connectionRequests()
    {
        $user = Auth::user();
        $received = ConnectionRequest::where('receiver_id', $user->id)->where('status', 0)->with(['senderDetail:user_id,fname,desg'])->orderBy('requested_at', 'desc')->get();
        $sent = ConnectionRequest::where('sender_id', $user->id)->where('status', 0)->with(['receiverDetail:user_id,fname,desg'])->orderBy('requested_at', 'desc')->get();

        return response()->json(['status' => true, 'message' => 'Connection requests fetched successfully', 'data' => ['received_requests' => $received, 'sent_requests' => $sent]], 200);
    }

    public function connectionCreate(Request $r)
    {
        $data = $r->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);
        $sender_id = Auth::id();
        if ($sender_id == $data['receiver_id']) {
            return response()->json(['status' => false, 'message' => 'You cannot send a request to yourself'], 400);
        }
        $existing = ConnectionRequest::where('sender_id', $sender_id)->where('receiver_id', $data['receiver_id'])->where('request_type', 'friend_request')->latest('requested_at')->first();
        if ($existing) {
            // Case 1: Pending or Accepted request already exists
            if (in_array($existing->status, [0, 2])) {
                return response()->json(['status' => false, 'message' => 'Request already sent or exists'], 409);
            }
            // Case 2: If Rejected (status = 1), check time gap
            if ($existing->status == 1) {
                $hoursPassed = now()->diffInHours($existing->requested_at);
                if ($hoursPassed < 24) {
                    $remaining = 24 - $hoursPassed;

                    return response()->json(['status' => false, 'message' => "You can send a new request after $remaining hour(s)"], 429);
                }
            }
        }
        $create_connection = ConnectionRequest::create(['sender_id' => $sender_id, 'receiver_id' => $data['receiver_id'], 'request_type' => 'friend_request', 'status' => 0, 'requested_at' => now()]);

        return response()->json(['status' => true, 'message' => 'Connection request sent successfully', 'data' => $create_connection], 201);
    }

    public function connectionAccept($rid)
    {
        $users = Auth::user();
        $request = ConnectionRequest::where('id', $rid)->where('status', 0)->where('receiver_id', $users->id)->first();
        if (! $request) {
            return response()->json(['status' => false, 'message' => 'Connection request not found or already responded!'], 404);
        }
        $request->status = 2;
        $request->responded_at = now();
        $request->save();
        Connection::create([
            'user_id' => $users->id,
            'friend_id' => $request->sender_id,
            'connected_at' => now(),
        ]);

        return response()->json(['status' => true, 'message' => 'Connection request accepted successfully', 'data' => $request], 200);
    }

    public function connectionReject($rid)
    {
        $users = Auth::user();
        $request = ConnectionRequest::where('id', $rid)->where('status', 0)->where('receiver_id', $users->id)->first();
        if (! $request) {
            return response()->json(['status' => false, 'message' => 'Connection request not found or already responded!'], 404);
        }
        $request->status = 1;
        $request->responded_at = now();
        $request->save();

        return response()->json(['status' => true, 'message' => 'Connection request rejected successfully', 'data' => $request], 200);
    }

    public function connectionRemove($rid)
    {
        $authId = Auth::id();
        // Find connection where user is either user_id or friend_id
        $connection = Connection::where('id', $rid)
            ->where(function ($q) use ($authId) {
                $q->where('user_id', $authId)->orWhere('friend_id', $authId);
            })->first();
        if (! $connection) {
            return response()->json(['status' => false, 'message' => 'Connection not found'], 404);
        }
        // Optional: Update ConnectionRequest if needed
        $request = ConnectionRequest::where(function ($q) use ($connection) {
            $q->where('sender_id', $connection->user_id)->where('receiver_id', $connection->friend_id)->orWhere(function ($q2) use ($connection) {
                $q2->where('sender_id', $connection->friend_id)->where('receiver_id', $connection->user_id);
            });
        })->where('status', 2)->first();
        if ($request) {
            $request->status = 1;
            $request->save();
        }
        $connection->delete();

        return response()->json(['status' => true, 'message' => 'Connection removed successfully'], 200);
    }

    // amit coding  start.................

    public function sendConnectionRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;

        // ❌ self request
        if ($senderId == $receiverId) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot send request to yourself',
            ], 400);
        }

        // ❌ already pending / accepted
        $already = ConnectionRequest::where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->whereIn('status', [0, 2])
            ->first();

        if ($already) {
            return response()->json([
                'status' => false,
                'message' => 'Request already exists',
            ], 409);
        }

        // ✅ create request
        $connection = ConnectionRequest::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 0, // pending
            'request_type' => 'friend_request',
            'requested_at' => now(),
        ]);

        // Send FCM notification to receiver
        $receiver = User::find($receiverId);
        if ($receiver && !empty($receiver->fcm_token)) {
            try {
                $serviceAccountPath = config('firebase.credentials');
                if (file_exists($serviceAccountPath)) {
                    $factory = (new Factory)->withServiceAccount($serviceAccountPath);
                    $messaging = $factory->createMessaging();

                    $senderUser = Auth::user();
                    $senderName = $senderUser->masterUser->fname ?? $senderUser->user_name ?? 'Someone';

                    $message = CloudMessage::fromArray([
                        'token' => $receiver->fcm_token,
                        'notification' => [
                            'title' => 'New Connection Request!',
                            'body'  => "{$senderName} has sent you a connection request."
                        ],
                        'data' => [
                            'type' => 'connection_request',
                            'senderId' => (string)$senderId
                        ]
                    ]);

                    $messaging->send($message);
                }
            } catch (\Exception $e) {
                // Log or ignore notification failure.
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Connection request sent successfully',
            'data' => $connection,
        ], 201);
    }

    public function getUniqueSendersByReceiver(Request $request, $receiverId)
    {
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'fname');
        $order = $request->query('order', 'asc');
        $perPage = (int) $request->query('per_page', 10);

        // allowed per_page
        $allowedPerPage = [5, 10, 15, 20];
        if (! in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Step 1: receiver ke saare sender ids nikaalo
        $senderIds = ConnectionRequest::where('receiver_id', $receiverId)
            ->pluck('sender_id')
            ->unique()
            ->toArray();

        if (empty($senderIds)) {
            return response()->json([
                'status' => true,
                'message' => 'No senders found',
                'count' => 0,
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                ],
                'data' => [],
            ], 200);
        }

        // Step 2: MasterUserDetail query
        $query = MasterUserDetail::with('user')
            ->whereIn('user_id', $senderIds);

        // ✅ Search
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('fname', 'LIKE', "%$search%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('user_name', 'LIKE', "%$search%");
                    });
            });
        }

        // ✅ Sort
        if ($sortBy === 'user_name') {
            $query->join('users', 'master_user_details.user_id', '=', 'users.id')
                ->orderBy('users.user_name', $order)
                ->select('master_user_details.*');
        } else {
            // allowed sort columns
            $allowedSort = ['fname', 'user_id'];
            if (! in_array($sortBy, $allowedSort)) {
                $sortBy = 'fname';
            }
            $query->orderBy($sortBy, $order);
        }

        // ✅ Pagination
        $senders = $query->paginate($perPage);

        // Step 3: status nikalna (connection_requests table se)
        $statuses = ConnectionRequest::where('receiver_id', $receiverId)
            ->whereIn('sender_id', $senderIds)
            ->get()
            ->keyBy('sender_id');

        // response data map
        $data = $senders->map(function ($detail) use ($statuses) {
            $status = $statuses[$detail->user_id]->status ?? 0;

            return [
                'sender_id' => $detail->user_id,
                'status' => $status,
                'fname' => $detail->fname,
                'user_name' => $detail->user->user_name ?? null,
                'profile_pic' => $detail->profile_pic,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Unique senders fetched successfully',
            'count' => $senders->total(),
            'pagination' => [
                'current_page' => $senders->currentPage(),
                'last_page' => $senders->lastPage(),
                'per_page' => $senders->perPage(),
                'next_page_url' => $senders->nextPageUrl(),
                'prev_page_url' => $senders->previousPageUrl(),
            ],
            'data' => $data,
        ], 200);
    }

    public function updateConnectionRequestStatus(Request $request)
    {
        $data = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'status' => 'required|in:1,2', // 1 = reject, 2 = accept
        ]);

        // 🔍 find pending request
        $connectionRequest = ConnectionRequest::where('sender_id', $data['sender_id'])
            ->where('receiver_id', $data['receiver_id'])
            ->where('status', 0) // only pending
            ->first();

        if (! $connectionRequest) {
            return response()->json([
                'status' => false,
                'message' => 'Pending connection request not found',
            ], 404);
        }

        // ✅ update status
        $connectionRequest->status = $data['status'];
        $connectionRequest->responded_at = now();
        $connectionRequest->save();

        // 🤝 if accepted → create connection
        if ($data['status'] == 2) {
            Connection::create([
                'user_id' => $data['receiver_id'],
                'friend_id' => $data['sender_id'],
                'connected_at' => now(),
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => $data['status'] == 2
                ? 'Connection request accepted successfully'
                : 'Connection request rejected successfully',
            'data' => $connectionRequest,
        ], 200);
    }

    public function getFriendListByUser(Request $request, $userId)
    {
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'fname'); // default
        $order = $request->query('order', 'asc');
        $perPage = (int) $request->query('per_page', 10);

        // limit allowed perPage
        $allowedPerPage = [5, 10, 15, 20];
        if (! in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // connections jaha user involved ho
        $connections = Connection::where('user_id', $userId)
            ->orWhere('friend_id', $userId)
            ->get();

        $friendIds = [];

        foreach ($connections as $connection) {
            $friendIds[] = ($connection->user_id == $userId)
                ? $connection->friend_id
                : $connection->user_id;
        }

        // duplicate remove
        $friendIds = array_unique($friendIds);

        // friends query
        $query = MasterUserDetail::with('user')
            ->whereIn('user_id', $friendIds);

        // ✅ Search
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('fname', 'LIKE', "%$search%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('user_name', 'LIKE', "%$search%");
                    });
            });
        }

        // ✅ Sort
        if ($sortBy == 'user_name') {
            $query->join('users', 'master_user_details.user_id', '=', 'users.id')
                ->orderBy('users.user_name', $order)
                ->select('master_user_details.*');
        } else {
            $query->orderBy($sortBy, $order);
        }

        // ✅ Pagination
        $friends = $query->paginate($perPage);

        // response format
        $data = $friends->map(function ($detail) {
            return [
                'friend_id' => $detail->user_id,
                'fname' => $detail->fname,
                'user_name' => $detail->user->user_name ?? null,
                'profile_pic' => $detail->profile_pic,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Friend list fetched successfully',
            'count' => $friends->total(),
            'pagination' => [
                'current_page' => $friends->currentPage(),
                'last_page' => $friends->lastPage(),
                'per_page' => $friends->perPage(),
                'next_page_url' => $friends->nextPageUrl(),
                'prev_page_url' => $friends->previousPageUrl(),
            ],
            'data' => $data,
        ], 200);
    }

    public function getUniqueReceiversBySender(Request $request, $senderId)
    {
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'fname');
        $order = $request->query('order', 'asc');
        $perPage = (int) $request->query('per_page', 10);

        // allowed per_page
        $allowedPerPage = [5, 10, 15, 20];
        if (! in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Step 1: sender ke saare unique receiver ids nikaalo (only pending status=0)
        $receiverIds = ConnectionRequest::where('sender_id', $senderId)
            ->where('status', 0) // ✅ only pending
            ->pluck('receiver_id')
            ->unique()
            ->toArray();

        if (empty($receiverIds)) {
            return response()->json([
                'status' => true,
                'message' => 'No receivers found',
                'count' => 0,
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                ],
                'data' => [],
            ], 200);
        }

        // Step 2: MasterUserDetail query
        $query = MasterUserDetail::with('user')
            ->whereIn('user_id', $receiverIds);

        // ✅ Search
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('fname', 'LIKE', "%$search%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('user_name', 'LIKE', "%$search%");
                    });
            });
        }

        // ✅ Sort
        if ($sortBy === 'user_name') {
            $query->join('users', 'master_user_details.user_id', '=', 'users.id')
                ->orderBy('users.user_name', $order)
                ->select('master_user_details.*');
        } else {
            // allowed sort columns
            $allowedSort = ['fname', 'user_id'];
            if (! in_array($sortBy, $allowedSort)) {
                $sortBy = 'fname';
            }
            $query->orderBy($sortBy, $order);
        }

        // ✅ Pagination
        $receivers = $query->paginate($perPage);

        // Step 3: statuses nikalna (connection_requests table se)..................
        $statuses = ConnectionRequest::where('sender_id', $senderId)
            ->whereIn('receiver_id', $receiverIds)
            ->where('status', 0)
            ->get()
            ->keyBy('receiver_id');

        // response data map
        $data = $receivers->getCollection()->map(function ($detail) use ($statuses) {
            $status = $statuses[$detail->user_id]->status ?? 0;

            return [
                'receiver_id' => $detail->user_id,
                'status' => $status,
                'fname' => $detail->fname,
                'user_name' => $detail->user->user_name ?? null,
                'profile_pic' => $detail->profile_pic,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Pending receivers fetched successfully',
            'count' => $receivers->total(),
            'pagination' => [
                'current_page' => $receivers->currentPage(),
                'last_page' => $receivers->lastPage(),
                'per_page' => $receivers->perPage(),
                'next_page_url' => $receivers->nextPageUrl(),
                'prev_page_url' => $receivers->previousPageUrl(),
            ],
            'data' => $data,
        ], 200);
    }

    public function deleteConnectionRequest(Request $request)
    {
        $data = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'status' => 'nullable|in:0,1,2',
        ]);

        // ✅ optional security: only sender/receiver hi delete kar sake
        $authId = Auth::id();

        if ($authId != $data['sender_id'] && $authId != $data['receiver_id']) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: You cannot delete this request',
            ], 403);
        }

        // 🔍 find request
        $query = ConnectionRequest::where('sender_id', $data['sender_id'])
            ->where('receiver_id', $data['receiver_id']);

        // ✅ agar status bhi pass hua hai to status bhi match karega
        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }

        $requestRow = $query->first();

        if (! $requestRow) {
            return response()->json([
                'status' => false,
                'message' => 'Connection request not found',
            ], 404);
        }

        // 🗑 delete
        $requestRow->delete();

        return response()->json([
            'status' => true,
            'message' => 'Connection request deleted successfully',
            'data' => [
                'sender_id' => $data['sender_id'],
                'receiver_id' => $data['receiver_id'],
                'status' => $requestRow->status,
            ],
        ], 200);
    }

    public function checkFriendship($userId, $viewerId)
    {
        // ❌ same user
        if ($userId == $viewerId) {
            return response()->json([
                'status' => true,
                'is_friend' => false,
                'message' => 'Same user',
            ], 200);
        }

        $exists = Connection::where(function ($q) use ($userId, $viewerId) {
            $q->where('user_id', $userId)
                ->where('friend_id', $viewerId);
        })->orWhere(function ($q) use ($userId, $viewerId) {
            $q->where('user_id', $viewerId)
                ->where('friend_id', $userId);
        })->exists();

        return response()->json([
            'status' => true,
            'is_friend' => $exists,
            'message' => $exists ? 'Both users are friends' : 'Not friends',
        ], 200);
    }

    public function removeConnection(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'friend_id' => 'required|integer',
        ]);

        $user_id = $request->user_id;
        $friend_id = $request->friend_id;

        // Use transaction to ensure both deletions happen or none...........
        DB::beginTransaction();

        try {
            // 1. Delete from connections table where (user_id, friend_id) in either order
            Connection::where(function ($query) use ($user_id, $friend_id) {
                $query->where('user_id', $user_id)
                    ->where('friend_id', $friend_id);
            })->orWhere(function ($query) use ($user_id, $friend_id) {
                $query->where('user_id', $friend_id)
                    ->where('friend_id', $user_id);
            })->delete();

            // 2. Delete from connection_requests table where (sender_id, receiver_id) in either order
            ConnectionRequest::where(function ($query) use ($user_id, $friend_id) {
                $query->where('sender_id', $user_id)
                    ->where('receiver_id', $friend_id);
            })->orWhere(function ($query) use ($user_id, $friend_id) {
                $query->where('sender_id', $friend_id)
                    ->where('receiver_id', $user_id);
            })->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Connection removed successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove connection.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
