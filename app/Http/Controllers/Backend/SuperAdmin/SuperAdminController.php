<?php

namespace App\Http\Controllers\Backend\SuperAdmin;

use App\Models\User;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Models\GeneratedContent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with([
                'message' => 'Old Password Does not Match!',
                'alert-type' => 'error'
            ]);
        }

        User::whereId($user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        Auth::logout();
        return redirect()->route('login');
    }

    public function Users(Request $request)
    {
        // Get filter parameters from request
        $roleFilter = $request->get('role', 'all');
        $search = $request->get('search', '');

        // Start building the query
        $query = User::whereIn('role', ['student', 'lecturer', 'admin']);

        // Apply role filter
        if ($roleFilter !== 'all' && in_array($roleFilter, ['student', 'lecturer', 'admin'])) {
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
                    
        return view('superadmin.superadmin_users', compact('users', 'roleFilter', 'search'));
    }

    public function DeleteUser($id)
    {
        User::findOrFail($id)->delete();

        return back()->with([
            'message' => 'User Deleted Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function Logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
