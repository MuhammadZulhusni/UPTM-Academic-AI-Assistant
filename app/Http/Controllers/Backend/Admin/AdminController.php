<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\User;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Models\GeneratedContent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function AdminDashboard()
    {
        // Fetch the authenticated user and their specific counts
        $user = User::withCount(['generatedContents', 'createdTemplates'])
                    ->find(Auth::id());

        // These counts remain global as requested by the user
        $newUsersCount = User::where('created_at', '>=', Carbon::now()->subWeeks(7))
            ->where('role', 'user')
            ->count();
        $totalUsers = User::whereIn('role', ['lecturer', 'admin'])->count();
        $totalTemplates = Template::count();

        // Get the counts for 'Student Template' and 'Lecturer Template'
        $studentTemplateCount = Template::where('category', 'Student')->count();
        $lecturerTemplateCount = Template::where('category', 'Lecturer')->count();

        // This count is now filtered to show documents created by the authenticated user
        $totalDocuments = GeneratedContent::where('user_id', Auth::id())->count();

        // This query is now filtered to show the latest templates created by the authenticated user
        $templates = $user->createdTemplates()->latest()->limit(6)->get();

        return view('admin.index', compact('user', 'newUsersCount', 'totalUsers', 'totalDocuments', 'totalTemplates', 'templates', 'studentTemplateCount', 'lecturerTemplateCount'));
    }

    /**
     * AdminLogout
     * Logs the authenticated admin user out of the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout();
        // Invalidate the current session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $notification = [
            'message' => 'Log Out Successfully',
            'alert-type' => 'success'
        ];

        return redirect('/login')->with($notification);
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

    /**
     * Displays the view for the admin to change password.
     *
     * @return \Illuminate\View\View
     */
    public function AdminChangePassword(){
        return view('admin.admin_change_password');
    }

    /**
     * Handles the logic for updating the admin's password.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function AdminPasswordUpdate(Request $request){

        $user = Auth::user(); 
        
        // Validate the incoming request data.
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        // Check if the old password matches the one in the database.
        if (!Hash::check($request->old_password, $user->password)) {
            $notification = array(
                'message' => 'Old Password Does not Match!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }

        // Update the user's password in the database.
        User::whereId($user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Store a flash message in the session to be displayed on the next request.
        session()->flash('message', 'Password Updated Successfully');
        session()->flash('alert-type', 'success');

        // Log the user out of the application.
        Auth::logout();

        return redirect()->route('login');
    }

    public function AdminUsers()
    {
        // Fetch all users with role 'student' or 'lecturer'
        $users = User::whereIn('role', ['student', 'lecturer'])->get(); 
        return view('admin.admin_users', compact('users'));
    }


    public function AdminDeleteUser($id)
    {
        // Find the user by ID and delete them.
        User::findOrFail($id)->delete();

        // Prepare a success notification message.
        $notification = [
            'message' => 'User Deleted Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }
}