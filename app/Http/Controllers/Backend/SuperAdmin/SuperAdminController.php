<?php

namespace App\Http\Controllers\Backend\SuperAdmin;

use App\Models\User;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Models\GeneratedContent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function Dashboard()
    {
        $user = User::withCount(['generatedContents', 'createdTemplates'])
            ->find(Auth::id());

        $newUsersCount = User::where('created_at', '>=', Carbon::now()->subWeeks(7))->count();
        $totalUsers = User::count();
        $totalTemplates = Template::count();
        $totalDocuments = GeneratedContent::count();
        $studentTemplateCount  = Template::where('category', 'Student')->count();
        $lecturerTemplateCount = Template::where('category', 'Lecturer')->count();
        $templates = Template::latest()->limit(6)->get();

        return view('superadmin.index', compact(
            'user',
            'newUsersCount',
            'totalUsers',
            'totalDocuments',
            'totalTemplates',
            'templates',
            'studentTemplateCount',
            'lecturerTemplateCount'
        ));
    }

    public function Profile()
    {
        $profileData = User::find(Auth::id());
        return view('superadmin.superadmin_profile', compact('profileData'));
    }

    public function ProfileStore(Request $request)
    {
        $id = Auth::id();
        $data = User::find($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        // Handle image upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // Optional: validate file size/type
            $request->validate([
                'photo' => 'image|mimes:jpg,jpeg,png|max:2048', // 2MB max
            ]);

            // Delete old image if exists
            if (!empty($data->photo) && file_exists(public_path('upload/superadmin_images/'.$data->photo))) {
                unlink(public_path('upload/superadmin_images/'.$data->photo));
            }

            // Save new image
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('upload/superadmin_images'), $filename);
            $data->photo = $filename;
        }

        $data->save();

        return back()->with([
            'message' => 'Super Admin Profile Updated Successfully',
            'alert-type' => 'success'
        ]);
    }


    public function ChangePassword()
    {
        return view('superadmin.superadmin_change_password');
    }

    public function PasswordUpdate(Request $request)
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

    public function Users(Request $request)
    {
        // Get filter parameters from request
        $roleFilter = $request->get('role', 'all');
        $statusFilter = $request->get('status', 'all');
        $search = $request->get('search', '');

        // Start building the query
        $query = User::whereIn('role', ['student', 'lecturer', 'admin']);

        // Apply role filter
        if ($roleFilter !== 'all' && in_array($roleFilter, ['student', 'lecturer', 'admin'])) {
            $query->where('role', $roleFilter);
        }

        // Apply status filter
        if ($statusFilter === 'active') {
            $query->where('is_active', 1);
        } elseif ($statusFilter === 'inactive') {
            $query->where('is_active', 0);
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
                    
        return view('superadmin.superadmin_users', compact('users', 'roleFilter', 'statusFilter', 'search'));
    }

    public function EditUser($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function UpdateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|in:student,lecturer,admin'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return back()->with([
            'message' => 'User Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function ToggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with([
            'message' => "User account has been {$status} successfully",
            'alert-type' => 'success'
        ]);
    }

    public function DeleteUser($id)
    {
        User::findOrFail($id)->delete();

        return back()->with([
            'message' => 'User Deleted Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function ResetPasswordPage()
    {
        $users = User::whereIn('role', ['student', 'lecturer', 'admin'])
                    ->select('id', 'name', 'email', 'role')
                    ->orderBy('name')
                    ->get();
        
        return view('superadmin.superadmin_reset_password', compact('users'));
    }

    public function SendResetLink(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);

        // Generate password reset token
        $token = Password::broker()->createToken($user);
        
        // Send password reset notification
        $user->sendPasswordResetNotification($token);

        return back()->with([
            'message' => "Password reset link has been sent to {$user->email}",
            'alert-type' => 'success'
        ]);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('q');
        
        $users = User::where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->where('id', '!=', Auth::id()) // Exclude current user if needed
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'email', 'role']);
        
        return response()->json($users);
    }

    public function Logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function CreateUser()
    {
        return view('superadmin.superadmin_create_user');
    }

    public function StoreUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|in:student,lecturer,admin',
            'is_active' => 'nullable|boolean'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('superadmin.users')->with([
            'message' => 'User Created Successfully',
            'alert-type' => 'success'
        ]);
    }
}