<?php

namespace App\Http\Controllers\Backend\Client;

use App\Models\User;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Models\GeneratedContent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    public function UserDashboard()
    {
        // Fetch the authenticated user and their counts
        $user = User::withCount(['generatedContents', 'createdTemplates'])
                    ->find(Auth::id());

        // Words used by the authenticated user
        $totalWordsUsed = $user->words_used;

        // Documents created by the authenticated user
        $totalDocuments = GeneratedContent::where('user_id', Auth::id())->count();

        // Determine the user's role (Student, Lecturer, etc.)
        $userRole = ucfirst($user->role);

        // Count templates available for the user's role
        $totalTemplates = Template::where('category', $userRole)->count();

        // Fetch latest 6 templates for the user's role
        $templates = Template::where('category', $userRole)
                            ->latest()
                            ->limit(6)
                            ->get();
        
        // Pass variables to the view
        return view('client.index', compact('user', 'totalWordsUsed', 'totalDocuments', 'totalTemplates', 'templates'));
    }

    public function UserLogout(Request $request){
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'Log Out Successfully',
            'alert-type' => 'success'
        );

        return redirect('/login')->with($notification);
    }
    //End Method 

public function UserProfile(){
    $id = Auth::user()->id;
    $profileData = User::find($id);
    return view('client.user_profile',compact('profileData'));
}

public function UserProfileStore(Request $request){
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

    $id = Auth::user()->id;
    $data = User::find($id);

    $data->name = $request->name;
    $data->email = $request->email;
    $data->phone = $request->phone;
    $data->address = $request->address;

    $oldPhotoPath = $data->photo;

    if ($request->hasFile('photo')) {
        try {
            $file = $request->file('photo');
            
            // Additional validation check
            if (!in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
                $notification = array(
                    'message' => 'Invalid image format! Only JPG, JPEG, and PNG are allowed.',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
            
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/user_images'), $filename);
            $data->photo = $filename;

            // Delete old image if exists and is different
            if ($oldPhotoPath && $oldPhotoPath !== $filename) {
                $this->deleteOldImage($oldPhotoPath);
            }
        } catch (\Exception $e) {
            $notification = array(
                'message' => 'Failed to upload image. Please try again.',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    $data->save();

    $notification = array(
        'message' => 'User Profile Updated Successfully',
        'alert-type' => 'success'
    );

    return redirect()->back()->with($notification);
}

private function deleteOldImage(string $oldPhotoPath) : void {
    $fullPath = public_path('upload/user_images/'.$oldPhotoPath);
    if (file_exists($fullPath)) {
        @unlink($fullPath); // @ suppresses warnings if file can't be deleted
    }
}

   public function UserChangePassword(){
     return view('client.user_change_password');
   }
    //End Method 

    public function UserPasswordUpdate(Request $request)
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

        Auth::logout();

        return redirect()->route('login')->with([
            'message' => 'Password updated successfully! Please login again.',
            'alert-type' => 'success'
        ]);
    }
}