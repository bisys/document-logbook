<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Delete a specific notification and redirect to its URL.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        $url = null;
        if (isset($notification->data['url'])) {
            $url = $notification->data['url'];
        }
        
        $notification->delete();

        if ($url) {
            return redirect($url);
        }

        return back();
    }

    /**
     * Delete all notifications for the authenticated user.
     */
    public function deleteAll()
    {
        Auth::user()->notifications()->delete();

        return back()->with('success', 'All notifications have been deleted.');
    }
}
