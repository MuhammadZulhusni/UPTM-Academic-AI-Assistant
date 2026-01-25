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
use App\Models\AdminActivity;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Artisan;

class SuperAdminController extends Controller
{
public function Dashboard()
{
    $user = User::withCount(['generatedContents', 'createdTemplates'])
        ->find(Auth::id());

    // User Statistics
    $totalUsers = User::count();
    $newUsersCount = User::where('created_at', '>=', Carbon::now()->subWeeks(7))->count();
    $activeUsersToday = User::whereHas('generatedContents', function($query) {
        $query->whereDate('created_at', Carbon::today());
    })->count();
    $activeUsersThisWeek = User::whereHas('generatedContents', function($query) {
        $query->where('created_at', '>=', Carbon::now()->subWeek());
    })->count();
    $activeUsersThisMonth = User::whereHas('generatedContents', function($query) {
        $query->where('created_at', '>=', Carbon::now()->subMonth());
    })->count();

    // Template Statistics
    $totalTemplates = Template::count();
    $activeTemplates = Template::where('is_active', 1)->count();
    $inactiveTemplates = Template::where('is_active', 0)->count();
    $studentTemplateCount = Template::where('category', 'Student')->count();
    $lecturerTemplateCount = Template::where('category', 'Lecturer')->count();
    
    // Document/Content Statistics
    $totalDocuments = GeneratedContent::count();
    $documentsToday = GeneratedContent::whereDate('created_at', Carbon::today())->count();
    $documentsThisWeek = GeneratedContent::where('created_at', '>=', Carbon::now()->subWeek())->count();
    $documentsThisMonth = GeneratedContent::where('created_at', '>=', Carbon::now()->subMonth())->count();
    $totalWordCount = GeneratedContent::sum('word_count');
    $avgWordsPerDocument = $totalDocuments > 0 ? round($totalWordCount / $totalDocuments) : 0;

    // Most Popular Templates (by usage)
    $popularTemplates = Template::withCount('generatedContents')
        ->orderBy('generated_contents_count', 'desc')
        ->limit(5)
        ->get();

    // Most Active Users (by document generation)
    $topUsers = User::withCount('generatedContents')
        ->orderBy('generated_contents_count', 'desc')
        ->limit(5)
        ->get();

    // System Health Metrics
    $systemSettings = DB::table('system_settings')->pluck('value', 'key');
    $documentRetentionDays = $systemSettings['document_retention_days'] ?? 90;
    $activityLogRetentionDays = $systemSettings['activity_log_retention_days'] ?? 30;
    $documentsToBeDeleted = GeneratedContent::where('created_at', '<=', Carbon::now()->subDays($documentRetentionDays))->count();

    // Recent Activity
    $recentDocuments = GeneratedContent::with(['user', 'template'])
        ->latest()
        ->limit(10)
        ->get();

    // Growth Rate Calculations (comparing this week vs last week)
    $lastWeekUsers = User::whereBetween('created_at', [
        Carbon::now()->subWeeks(2),
        Carbon::now()->subWeek()
    ])->count();
    $userGrowthRate = $lastWeekUsers > 0 ? round((($newUsersCount - $lastWeekUsers) / $lastWeekUsers) * 100, 1) : 0;

    $lastWeekDocuments = GeneratedContent::whereBetween('created_at', [
        Carbon::now()->subWeeks(2),
        Carbon::now()->subWeek()
    ])->count();
    $documentGrowthRate = $lastWeekDocuments > 0 ? round((($documentsThisWeek - $lastWeekDocuments) / $lastWeekDocuments) * 100, 1) : 0;

    // Latest templates for display
    $templates = Template::latest()->limit(6)->get();

    return view('superadmin.index', compact(
        'user',
        'totalUsers',
        'newUsersCount',
        'activeUsersToday',
        'activeUsersThisWeek',
        'activeUsersThisMonth',
        'totalTemplates',
        'activeTemplates',
        'inactiveTemplates',
        'studentTemplateCount',
        'lecturerTemplateCount',
        'totalDocuments',
        'documentsToday',
        'documentsThisWeek',
        'documentsThisMonth',
        'totalWordCount',
        'avgWordsPerDocument',
        'popularTemplates',
        'topUsers',
        'documentRetentionDays',
        'activityLogRetentionDays',
        'documentsToBeDeleted',
        'recentDocuments',
        'userGrowthRate',
        'documentGrowthRate',
        'templates'
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

        // Step 1: Validate all password fields with comprehensive rules
        $request->validate([
            'old_password' => 'required',
            'new_password' => [
                'required',
                'confirmed',
                'min:8',
                'max:64',
                'regex:/[a-z]/',      // at least one lowercase letter
                'regex:/[A-Z]/',      // at least one uppercase letter
                'regex:/[0-9]/',      // at least one number
                'regex:/[@$!%*#?&]/', // at least one special character
            ],
        ], [
            // Custom error messages
            'new_password.min' => 'Password must be at least 8 characters long.',
            'new_password.max' => 'Password must not exceed 64 characters.',
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
            'new_password.confirmed' => 'Password confirmation does not match.',
        ]);

        // Step 2: Check if Current Password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors([
                'old_password' => 'Current password is incorrect.',
            ])->withInput();
        }

        // Step 3: Check if new password is different from old password
        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors([
                'new_password' => 'New password must be different from your current password.',
            ])->withInput();
        }

        // Step 4: Update new password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        Auth::logout();

        $notification = array(
            'message' => 'Password updated successfully! Please login again with your new password.',
            'alert-type' => 'success'
        );

        return redirect()->route('login')->with($notification);
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

    /**
     * Toggle user account active status
     * This method activates or deactivates a user account
     */
    public function ToggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        // Toggle the user's active status
        // If currently active → deactivate
        // If currently inactive → activate
        $user->is_active = !$user->is_active;
        $user->save();

        // Set readable status message based on new state
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
        // Retrieve users with allowed roles only
        // Roles included: student, lecturer, and admin
        $users = User::whereIn('role', ['student', 'lecturer', 'admin'])
                    // Select only necessary fields to reduce data load
                    ->select('id', 'name', 'email', 'role')
                    // Sort users alphabetically by name
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

    /**
     * Search users based on name or email
     */
    public function searchUsers(Request $request)
    {
         // Get search keyword from request (e.g. typed text)
        $query = $request->input('q');
        
        // Search users where name OR email matches the keyword
        $users = User::where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%");
            })
            // Exclude currently logged-in user from search result
            ->where('id', '!=', Auth::id()) // Exclude current user if needed
            // Sort result alphabetically by name
            ->orderBy('name')
             // Limit result to 10 users for performance
            ->limit(10)
            // Return only required fields
            ->get(['id', 'name', 'email', 'role']);
        
        return response()->json($users);
    }

    public function Logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'Log Out Successfully',
            'alert-type' => 'success'
        );

        return redirect('/login')->with($notification);
    }

    public function CreateUser()
    {
        return view('superadmin.superadmin_create_user');
    }

    public function StoreUser(Request $request)
    {
        try {
            // Validate all fields with enhanced password validation
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    'confirmed',
                    'min:8',
                    'max:64',
                    'regex:/[a-z]/',      // at least one lowercase letter
                    'regex:/[A-Z]/',      // at least one uppercase letter
                    'regex:/[0-9]/',      // at least one number
                    'regex:/[@$!%*#?&]/', // at least one special character
                ],
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'role' => 'required|in:student,lecturer,admin',
                'is_active' => 'nullable|boolean'
            ], [
                // Custom error messages
                'name.required' => 'Full name is required.',
                'name.max' => 'Full name must not exceed 255 characters.',
                'email.required' => 'Email address is required.',
                'email.email' => 'Please provide a valid email address.',
                'email.unique' => 'This email address is already registered.',
                'password.required' => 'Password is required.',
                'password.confirmed' => 'Password confirmation does not match.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.max' => 'Password must not exceed 64 characters.',
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*#?&).',
                'phone.max' => 'Phone number must not exceed 20 characters.',
                'address.max' => 'Address must not exceed 500 characters.',
                'role.required' => 'User role is required.',
                'role.in' => 'Invalid user role selected.',
            ]);

            // Create the user
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'role' => $request->role,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            $notification = array(
                'message' => 'User created successfully!',
                'alert-type' => 'success'
            );

            return redirect()->route('superadmin.users')->with($notification);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $notification = array(
                'message' => 'Please check the form fields and correct the errors.',
                'alert-type' => 'error'
            );

            return redirect()->back()->withErrors($e->validator)->withInput()->with($notification);

        } catch (\Exception $e) {
            $notification = array(
                'message' => 'An error occurred while creating the user. Please try again.',
                'alert-type' => 'error'
            );

            return redirect()->back()->withInput()->with($notification);
        }
    }

    public function AdminActivities(Request $request)
    {
        $adminFilter = $request->get('admin', 'all');
        $activityFilter = $request->get('activity', 'all');
        $dateFilter = $request->get('date', 'all');
        $search = $request->get('search', '');

        // Get ONLY ACTIVE admins for filter dropdown
        $admins = User::where('role', 'admin')
            ->where('is_active', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Build main query (ONLY ACTIVE admins)
        $query = AdminActivity::with('admin')
            ->whereHas('admin', function ($q) {
                $q->where('role', 'admin')
                ->where('is_active', 1);
            });

        // Apply admin filter
        if ($adminFilter !== 'all') {
            $query->where('admin_id', $adminFilter);
        }

        // Apply activity type filter
        if ($activityFilter !== 'all') {
            $query->where('activity_type', $activityFilter);
        }

        // Apply date filter
        if ($dateFilter !== 'all') {
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;

                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;

                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subWeek());
                    break;

                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->subMonth());
                    break;
            }
        }

        // Apply search (activity description OR admin name)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('activity_description', 'like', "%{$search}%")
                ->orWhereHas('admin', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Get activities with pagination
        $activities = $query->latest()
            ->paginate(10)
            ->withQueryString();

        // Statistics (ONLY ACTIVE admins)
        $stats = [
            'total_activities' => AdminActivity::whereHas('admin', function ($q) {
                $q->where('role', 'admin')
                ->where('is_active', 1);
            })->count(),

            'today_activities' => AdminActivity::whereHas('admin', function ($q) {
                $q->where('role', 'admin')
                ->where('is_active', 1);
            })->whereDate('created_at', Carbon::today())->count(),

            'total_admins' => User::where('role', 'admin')
                ->where('is_active', 1)
                ->count(),

            'templates_created' => AdminActivity::where('activity_type', 'template_created')
                ->whereHas('admin', function ($q) {
                    $q->where('role', 'admin')
                    ->where('is_active', 1);
                })->count(),

            'users_created' => AdminActivity::where('activity_type', 'user_created')
                ->whereHas('admin', function ($q) {
                    $q->where('role', 'admin')
                    ->where('is_active', 1);
                })->count(),
        ];

        return view('superadmin.admin_activities', compact(
            'activities',
            'admins',
            'adminFilter',
            'activityFilter',
            'dateFilter',
            'search',
            'stats'
        ));
    }

    public function AdminActivityDetails($id)
    {
        $activity = AdminActivity::with('admin')->findOrFail($id);
        return response()->json($activity);
    }

    public function ExportAdminActivities(Request $request)
    {
        // Export to CSV
        $activities = AdminActivity::with('admin')
            ->whereHas('admin', function($q) {
                $q->where('role', 'admin');
            })
            ->latest()
            ->get();

        $filename = 'admin_activities_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Date', 'Time', 'Day', 'Admin Name', 'Activity Type', 'Description']);
            
            // Add data
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->formatted_date,
                    $activity->formatted_time,
                    $activity->day_name,
                    $activity->admin->name,
                    str_replace('_', ' ', ucwords($activity->activity_type)),
                    $activity->activity_description,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show activity settings page
     */
    public function ActivitySettings()
    {
        $retentionDays = SystemSetting::getActivityLogRetentionDays();
        $autoCleanup = SystemSetting::isAutoCleanupEnabled();
        
        // Get statistics
        $totalLogs = AdminActivity::count();
        $oldestLog = AdminActivity::oldest()->first();
        $newestLog = AdminActivity::latest()->first();
        
        // Calculate logs that would be deleted
        $cutoffDate = Carbon::now()->subDays($retentionDays);
        $logsToDelete = AdminActivity::where('created_at', '<', $cutoffDate)->count();
        
        return view('superadmin.activity_settings', compact(
            'retentionDays',
            'autoCleanup',
            'totalLogs',
            'oldestLog',
            'newestLog',
            'logsToDelete'
        ));
    }

    /**
     * Update activity settings
     */
    public function UpdateActivitySettings(Request $request)
    {
        $request->validate([
            'retention_days' => 'required|integer|min:1|max:365',
        ]);

        // Update retention days
        SystemSetting::set(
            'activity_log_retention_days',
            $request->retention_days,
            'integer',
            'Number of days to keep admin activity logs before auto-deletion'
        );

        // Always enable auto cleanup when settings are saved
        SystemSetting::set(
            'activity_log_auto_cleanup',
            1,
            'boolean',
            'Enable automatic cleanup of old activity logs'
        );

        return back()->with([
            'message' => 'Settings saved! Auto-cleanup is now active and will run every night at midnight.',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Manually trigger cleanup of admin activity logs
     * This method deletes activity logs older than the selected number of days
     */
    public function ManualCleanup(Request $request)
    {
        // Validate user input:
        // - days is required
        // - must be an integer
        // - minimum 1 day, maximum 365 days
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        // Calculate the cutoff date based on the selected number of days
        // Example: if days = 30, delete logs older than 30 days
        try {
            $cutoffDate = Carbon::now()->subDays($request->days);
            // Count how many activity logs are older than the cutoff date
            $count = AdminActivity::where('created_at', '<', $cutoffDate)->count();

             // If no old activity logs are found, return an info message
            if ($count === 0) {
                return back()->with([
                    'message' => 'No activity logs found older than ' . $request->days . ' days',
                    'alert-type' => 'info'
                ]);
            }

            // Delete all activity logs older than the cutoff date
            $deleted = AdminActivity::where('created_at', '<', $cutoffDate)->delete();

            // Return success message with total deleted records
            return back()->with([
                'message' => "Successfully deleted {$deleted} old activity log(s)",
                'alert-type' => 'success'
            ]);

        } catch (\Exception $e) {
            // Handle any unexpected errors during the cleanup process
            return back()->with([
                'message' => 'Error during cleanup: ' . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }
}