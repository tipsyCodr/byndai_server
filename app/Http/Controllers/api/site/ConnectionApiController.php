<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Connection;
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
}
