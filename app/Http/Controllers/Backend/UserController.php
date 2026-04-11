<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MasterUserDetail;
use App\Models\UserSubscription;
use App\Models\UserSubscriptionFeature;
use App\Models\UserVideo;
use App\Models\UserLink;
use App\Models\UserReview;
use App\Models\BusinessInfo;
use App\Models\Connection;
use App\Models\MasterEvent;
use App\Models\MasterEventImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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

    public function destroy($id)
    {
        try {
            $user = User::with(['subscriptions', 'businesses', 'userLinks', 'masterUser'])->findOrFail($id);

            DB::beginTransaction();

            // 1. Delete User ID Card Images from storage
            $this->deletePhysicalFile($user->bni_id_card_image);
            $this->deletePhysicalFile($user->jci_id_card_image);

            // 2. Delete User Videos & physical files
            $videos = UserVideo::where('user_id', $user->id)->get();
            foreach ($videos as $video) {
                if ($video->video_url) {
                    $this->deletePhysicalFile($video->video_url);
                }
                $video->delete();
            }

            // 3. Delete Subscriptions & Features
            $subscriptionIds = UserSubscription::where('user_id', $user->id)->pluck('id');
            UserSubscriptionFeature::whereIn('user_subscription_id', $subscriptionIds)->delete();
            UserSubscription::where('user_id', $user->id)->delete();

            // 4. Delete Business Info & potential business-related files (if any)
            BusinessInfo::where('user_id', $user->id)->delete();

            // 5. Delete Connections (both ways)
            Connection::where('user_id', $user->id)->orWhere('friend_id', $user->id)->delete();

            // 6. Delete Links & Reviews
            UserLink::where('user_id', $user->id)->delete();
            UserReview::where('user_id', $user->id)->delete();

            // 7. Delete Events created by user & their images
            $events = MasterEvent::where('created_by', $user->id)->get();
            foreach ($events as $event) {
                // Delete event images physical files
                $eventImages = MasterEventImage::where('event_id', $event->id)->get();
                foreach ($eventImages as $img) {
                    $this->deletePhysicalFile($img->event_img_path);
                    $img->delete();
                }

                // Delete event folder
                $folderTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $event->title);
                if (Storage::disk('public')->exists("event/{$folderTitle}")) {
                    Storage::disk('public')->deleteDirectory("event/{$folderTitle}");
                }
                
                $event->delete();
            }

            // 8. Delete Master User Detail
            MasterUserDetail::where('user_id', $user->id)->delete();

            // 9. Finally delete the User
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'User and all related data deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User Deletion Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    private function deletePhysicalFile($path)
    {
        if (!$path) return;

        // Strip asset() or storage/ prefix if it's a relative path
        $relativePath = str_replace(['storage/', asset('storage/')], '', $path);
        
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        } elseif (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
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
