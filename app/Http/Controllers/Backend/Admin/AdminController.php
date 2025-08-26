<?php

namespace App\Http\Controllers\Backend\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * AdminLogout
     * Logs the authenticated admin user out of the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function AdminLogout(Request $request)
    {
        // Log out the user from the 'web' guard
        Auth::guard('web')->logout();
        // Invalidate the current session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * AdminProfile
     * Fetches and displays the profile data for the authenticated admin.
     *
     * @return \Illuminate\View\View
     */
    public function AdminProfile()
    {
        // Get the ID of the currently authenticated user
        $id = Auth::user()->id;
        $profileData = User::find($id);

        return view('admin.admin_profile', compact('profileData'));
    }

    /**
     * AdminProfileStore
     * Handles the updating of the authenticated admin's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function AdminProfileStore(Request $request)
    {
        // Get the ID of the authenticated user
        $id = Auth::user()->id;
        $data = User::find($id);

        // Update the user data with values from the request
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        // Store the path of the old photo for potential deletion
        $oldPhotoPath = $data->photo;

        // Check if a new photo has been uploaded
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');     
            
            // Generate a unique filename based on the current time
            $filename = time().'.'.$file->getClientOriginalExtension();

            // Move the new file to the public 'upload/admin_images' directory
            $file->move(public_path('upload/admin_images'), $filename);

            // Update the photo field with the new filename
            $data->photo = $filename;

            // Delete the old image if it exists and is not the same as the new one
            if ($oldPhotoPath && $oldPhotoPath !== $filename) {
               $this->deleteOldImage($oldPhotoPath);
            } 
        }

        $data->save();

        $notification = [
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    /**
     * deleteOldImage
     * Deletes the old image file from the server's file system.
     *
     * @param string $oldPhotoPath The filename of the old photo.
     * @return void
     */
    private function deleteOldImage(string $oldPhotoPath) : void
    {
        // Construct the full path to the old image file
        $fullPath = public_path('upload/admin_images/'.$oldPhotoPath);

        // Check if the file exists before attempting to delete it
        if (file_exists($fullPath)) {
           unlink($fullPath);
        }
    }
}
