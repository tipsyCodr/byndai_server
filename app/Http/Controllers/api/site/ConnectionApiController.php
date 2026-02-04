<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\User;
use App\Models\MasterUserDetail;
use App\Models\ConnectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'receiver_id' => 'required|exists:users,id'
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
        $create_connection = ConnectionRequest::create(['sender_id' => $sender_id, 'receiver_id' => $data['receiver_id'], 'request_type' => 'friend_request', 'status' => 0, 'requested_at' => now(),]);
        return response()->json(['status' => true, 'message' => 'Connection request sent successfully', 'data' => $create_connection], 201);
    }
    public function connectionAccept($rid)
    {
        $users = Auth::user();
        $request = ConnectionRequest::where('id', $rid)->where('status', 0)->where('receiver_id', $users->id)->first();
        if (!$request) {
            return response()->json(['status' => false, 'message' => 'Connection request not found or already responded!'], 404);
        }
        $request->status = 2;
        $request->responded_at = now();
        $request->save();
        Connection::create([
            'user_id' => $users->id,
            'friend_id' => $request->sender_id,
            'connected_at' => now()
        ]);
        return response()->json(['status' => true, 'message' => 'Connection request accepted successfully', 'data' => $request], 200);
    }
    public function connectionReject($rid)
    {
        $users = Auth::user();
        $request = ConnectionRequest::where('id', $rid)->where('status', 0)->where('receiver_id', $users->id)->first();
        if (!$request) {
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
        if (!$connection) {
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


    //amit coding  start


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
                'message' => 'You cannot send request to yourself'
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
                'message' => 'Request already exists'
            ], 409);
        }
    
        // ✅ create request
        $connection = ConnectionRequest::create([
            'sender_id'    => $senderId,
            'receiver_id'  => $receiverId,
            'status'       => 0, // pending
            'request_type' => 'friend_request',
            'requested_at' => now(),
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Connection request sent successfully',
            'data' => $connection
        ], 201);
    }



public function getUniqueSendersByReceiver($receiverId)
{
    // Step 1: Get all unique sender_ids for this receiver
    $senders = ConnectionRequest::where('receiver_id', $receiverId)
        ->get(['sender_id', 'status']); // status bhi fetch karlo

    $responseData = [];

    foreach ($senders as $sender) {
        // Step 2: Get MasterUserDetail with related User info
        $userDetail = MasterUserDetail::with('user')
            ->where('user_id', $sender->sender_id)
            ->first();

        if ($userDetail) {
            $responseData[] = [
                'sender_id' => $sender->sender_id,
                'status'    => $sender->status,
                'fname'     => $userDetail->fname,
                'user_name' => $userDetail->user->user_name ?? null,
            ];
        }
    }

    return response()->json([
        'status'  => true,
        'message' => 'Unique senders fetched successfully',
        'count'   => count($responseData),
        'data'    => $responseData
    ], 200);
}


public function updateConnectionRequestStatus(Request $request)
{
    $data = $request->validate([
        'sender_id'   => 'required|exists:users,id',
        'receiver_id' => 'required|exists:users,id',
        'status'      => 'required|in:1,2' // 1 = reject, 2 = accept
    ]);

    // 🔍 find pending request
    $connectionRequest = ConnectionRequest::where('sender_id', $data['sender_id'])
        ->where('receiver_id', $data['receiver_id'])
        ->where('status', 0) // only pending
        ->first();

    if (!$connectionRequest) {
        return response()->json([
            'status' => false,
            'message' => 'Pending connection request not found'
        ], 404);
    }

    // ✅ update status
    $connectionRequest->status = $data['status'];
    $connectionRequest->responded_at = now();
    $connectionRequest->save();

    // 🤝 if accepted → create connection
    if ($data['status'] == 2) {
        Connection::create([
            'user_id'    => $data['receiver_id'],
            'friend_id'  => $data['sender_id'],
            'connected_at' => now()
        ]);
    }

    return response()->json([
        'status'  => true,
        'message' => $data['status'] == 2
            ? 'Connection request accepted successfully'
            : 'Connection request rejected successfully',
        'data'    => $connectionRequest
    ], 200);
}

public function getFriendListByUser($userId)
{
    // 🔹 all connections jaha user involved ho
    $connections = Connection::where('user_id', $userId)
        ->orWhere('friend_id', $userId)
        ->get();

    $friends = [];

    foreach ($connections as $connection) {

        // 🧠 identify friend id
        $friendId = $connection->user_id == $userId
            ? $connection->friend_id
            : $connection->user_id;

        // 🔍 get friend detail
        $detail = MasterUserDetail::with('user')
            ->where('user_id', $friendId)
            ->first();

        if ($detail) {
            $friends[] = [
                'friend_id' => $friendId,
                'fname'     => $detail->fname,
                'user_name' => $detail->user->user_name ?? null,
            ];
        }
    }

    return response()->json([
        'status'  => true,
        'message' => 'Friend list fetched successfully',
        'count'   => count($friends),
        'data'    => $friends
    ], 200);
}

public function getUniqueReceiversBySender($senderId)
{
    $receivers = ConnectionRequest::where('sender_id', $senderId)
        ->where('status', 0) // ✅ only pending
        ->get(['receiver_id', 'status']);

    $responseData = [];

    foreach ($receivers as $receiver) {

        $userDetail = MasterUserDetail::with('user')
            ->where('user_id', $receiver->receiver_id)
            ->first();

        if ($userDetail) {
            $responseData[] = [
                'receiver_id' => $receiver->receiver_id,
                'status'      => $receiver->status,
                'fname'       => $userDetail->fname,
                'user_name'   => $userDetail->user->user_name ?? null,
            ];
        }
    }

    return response()->json([
        'status'  => true,
        'message' => 'Pending receivers fetched successfully',
        'count'   => count($responseData),
        'data'    => $responseData
    ], 200);
}

public function deleteConnectionRequest(Request $request)
{
    $data = $request->validate([
        'sender_id'   => 'required|exists:users,id',
        'receiver_id' => 'required|exists:users,id',
        'status'      => 'nullable|in:0,1,2'
    ]);

    // ✅ optional security: only sender/receiver hi delete kar sake
    $authId = Auth::id();

    if ($authId != $data['sender_id'] && $authId != $data['receiver_id']) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized: You cannot delete this request'
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

    if (!$requestRow) {
        return response()->json([
            'status' => false,
            'message' => 'Connection request not found'
        ], 404);
    }

    // 🗑 delete
    $requestRow->delete();

    return response()->json([
        'status'  => true,
        'message' => 'Connection request deleted successfully',
        'data'    => [
            'sender_id'   => $data['sender_id'],
            'receiver_id' => $data['receiver_id'],
            'status'      => $requestRow->status,
        ]
    ], 200);
}

    
}
