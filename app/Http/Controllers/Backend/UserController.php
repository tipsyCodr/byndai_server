<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MasterUserDetail;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('masterUser')->orderBy('created_at', 'desc');

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%");
            });
        }

        $users = $query->paginate(15);

        $users->getCollection()->transform(function ($user) {
            $user->bni_card_url = $this->getMediaUrl($user->bni_id_card_image);
            $user->jci_card_url = $this->getMediaUrl($user->jci_id_card_image);
            return $user;
        });

        return view('backend.users.index', compact('users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bni_member' => 'sometimes|boolean',
            'jci_member' => 'sometimes|boolean',
        ]);

        $user = User::findOrFail($id);

        $detail = MasterUserDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'bni_member' => $request->has('bni_member') ? 1 : 0,
                'jci_member' => $request->has('jci_member') ? 1 : 0,
            ]
        );

        return redirect()->back()->with('success', 'User badges updated successfully.');
    }

    private function getMediaUrl($path)
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
