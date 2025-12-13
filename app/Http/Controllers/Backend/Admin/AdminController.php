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
use App\Traits\LogsAdminActivity; // Activities logging trait

class AdminController extends Controller
{
    use LogsAdminActivity; // Activities logging trait

    public function AdminDashboard()
    {
        // Fetch the authenticated user and their specific counts
        $user = User::withCount(['generatedContents', 'createdTemplates'])
                    ->find(Auth::id());

        // These counts remain global as requested by the user
        $newUsersCount = User::where('created_at', '>=', Carbon::now()->subWeeks(7))
            ->where('role', 'user')
            ->count();
        $totalUsers = User::whereIn('role', ['lecturer', 'student'])->count();
        $totalTemplates = Template::count();

        // Get the counts for 'Student Template' and 'Lecturer Template'
        $studentTemplateCount = Template::where('category', 'Student')->count();
        $lecturerTemplateCount = Template::where('category', 'Lecturer')->count();

        // This count is now filtered to show documents created by the authenticated user
        $totalDocuments = GeneratedContent::where('user_id', Auth::id())->count();

        // Show latest templates from ALL admins
        $templates = Template::latest()->limit(6)->get();

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
        // Log the logout activity BEFORE logging out
        $this->logLogout();

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
        // Validate input with custom messages
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048', // max 2MB
        ], [
            'photo.image' => 'The file must be an image.',
            'photo.mimes' => 'Only JPG, JPEG, and PNG formats are allowed.',
            'photo.max' => 'Image size must not exceed 2MB.',
        ]);

        // Get the ID of the authenticated user
        $id = Auth::user()->id;
        $data = User::find($id);

        // Store old data for potential logging (optional - profile updates typically aren't logged)
        // But keeping it here in case  want to track profile changes
        $oldData = [
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'address' => $data->address,
        ];

        // Update the user data with values from the request
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        // Store the path of the old photo for potential deletion
        $oldPhotoPath = $data->photo;

        // Check if a new photo has been uploaded
        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                
                // Additional validation check
                if (!in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
                    $notification = [
                        'message' => 'Invalid image format! Only JPG, JPEG, and PNG are allowed.',
                        'alert-type' => 'error'
                    ];
                    return redirect()->back()->with($notification);
                }
                
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
            } catch (\Exception $e) {
                $notification = [
                    'message' => 'Failed to upload image. Please try again.',
                    'alert-type' => 'error'
                ];
                return redirect()->back()->with($notification);
            }
        }

        $data->save();

        // Optional: Log profile update (uncomment if you want to track this)
        // $this->logActivity(
        //     'profile_updated',
        //     "Updated own profile",
        //     'user',
        //     $data->id,
        //     [
        //         'old_name' => $oldData['name'],
        //         'new_name' => $data->name,
        //         'old_email' => $oldData['email'],
        //         'new_email' => $data->email,
        //     ]
        // );

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
            @unlink($fullPath); // @ suppresses warnings if file can't be deleted
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

    public function AdminPasswordUpdate(Request $request)
    {
        $user = Auth::user();

        // Step 1: Validate the Current Password is entered
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        // Step 2: Check if Current Password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors([
                'old_password' => 'Current password is incorrect.',
            ])->withInput();
        }

        // Step 3: Update new password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Log password change activity (before logout)
        $this->logActivity(
            'password_changed',
            "Changed own password",
            'user',
            $user->id
        );

        Auth::logout();

        return redirect()->route('login')->with([
            'message' => 'Password updated successfully! Please login again.',
            'alert-type' => 'success'
        ]);
    }

    public function AdminUsers(Request $request)
    {
        // Get filter parameters from request
        $roleFilter = $request->get('role', 'all');
        $search = $request->get('search', '');

        // Start building the query
        $query = User::whereIn('role', ['student', 'lecturer']);

        // Apply role filter
        if ($roleFilter !== 'all' && in_array($roleFilter, ['student', 'lecturer'])) {
            $query->where('role', $roleFilter);
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Order by latest and paginate with query string preserved
        $users = $query->latest()
                    ->paginate(10)
                    ->withQueryString();
        
        return view('admin.admin_users', compact('users', 'roleFilter', 'search'));
    }

    public function AdminDeleteUser($id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);
        
        // Store user info for logging
        $userName = $user->name;
        $userEmail = $user->email;
        
        // Delete the user
        $user->delete();

        // Log the activity
        $this->logUserDeleted($userName, $userEmail, $id);

        // Prepare a success notification message.
        $notification = [
            'message' => 'User Deleted Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }
}