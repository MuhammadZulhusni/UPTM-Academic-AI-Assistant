<?php

namespace App\Http\Controllers\Backend\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\InactiveUserNudge;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\InactiveUserNudgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SuperAdminInactiveUserNudgeController extends Controller
{
    public function index(InactiveUserNudgeService $service)
    {
        $idleDays = $service->idleDays();
        $enabled = InactiveUserNudgeService::isEnabled();

        $users = User::query()
            ->whereIn('role', ['student', 'lecturer'])
            ->orderBy('name')
            ->get()
            ->map(function (User $user) use ($service) {
                $user->idle_days = $service->daysInactive($user);

                return $user;
            });

        $nudges = InactiveUserNudge::with('user')->latest()->limit(20)->get();

        return view('superadmin.inactive_user_reminders', compact(
            'idleDays',
            'enabled',
            'users',
            'nudges'
        ));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'idle_days' => 'required|integer|min:1|max:90',
            'enabled' => 'nullable|boolean',
        ]);

        SystemSetting::set(
            'inactive_user_nudge_idle_days',
            (int) $request->idle_days,
            'integer',
            'Idle days before a student or lecturer is included in the Monday reminder job'
        );

        SystemSetting::set(
            'inactive_user_nudge_enabled',
            $request->boolean('enabled'),
            'boolean',
            'Send AI-written reminder emails to idle students and lecturers on Mondays at 09:00'
        );

        return redirect()->route('superadmin.inactive.nudge.index')->with([
            'message' => 'Reminder settings saved.',
            'alert-type' => 'success',
        ]);
    }

    public function previewHtml(Request $request, InactiveUserNudgeService $service)
    {
        $user = null;
        if ($request->filled('user_id')) {
            $user = User::query()
                ->whereIn('role', ['student', 'lecturer'])
                ->find($request->user_id);
        }

        if ($user) {
            $copy = $service->preview($user);
            $userName = $user->name;
        } else {
            $sample = new User(['name' => 'Student', 'role' => 'student']);
            $copy = $service->buildFallbackEmail($sample, $service->idleDays());
            $userName = 'Student';
        }

        return response()->view('emails.inactive_user_nudge', [
            'userName' => $userName,
            'subjectLine' => $copy['subject'],
            'bodyText' => $copy['body'],
            'logoSrc' => asset('upload/uptm.png'),
        ]);
    }

    public function sendToUser(Request $request, InactiveUserNudgeService $service)
    {
        $user = $this->selectedUser($request);
        $nudge = $service->sendToUser($user, 'manual');

        return redirect()
            ->route('superadmin.inactive.nudge.index', ['user_id' => $user->id])
            ->with([
                'message' => $nudge->status === 'sent'
                    ? "Reminder sent to {$user->email}."
                    : 'Could not send the reminder. Check mail settings and laravel.log.',
                'alert-type' => $nudge->status === 'sent' ? 'success' : 'error',
            ]);
    }

    public function sendPreviewToMe(Request $request, InactiveUserNudgeService $service)
    {
        $user = $this->selectedUser($request);

        try {
            $service->sendPreviewToAddress($user, Auth::user()->email);
        } catch (\Throwable $e) {
            return redirect()
                ->route('superadmin.inactive.nudge.index', ['user_id' => $user->id])
                ->with([
                    'message' => 'Could not send preview to your inbox: '.$e->getMessage(),
                    'alert-type' => 'error',
                ]);
        }

        return redirect()
            ->route('superadmin.inactive.nudge.index', ['user_id' => $user->id])
            ->with([
                'message' => 'Preview sent to '.Auth::user()->email.'. If MAIL_MAILER=log, open storage/logs/laravel.log.',
                'alert-type' => 'success',
            ]);
    }

    public function clear(Request $request)
    {
        $validated = $request->validate([
            'nudge_ids' => 'required|array|min:1',
            'nudge_ids.*' => 'integer|exists:inactive_user_nudges,id',
        ]);

        $count = InactiveUserNudge::whereIn('id', $validated['nudge_ids'])->delete();

        return redirect()->route('superadmin.inactive.nudge.index')->with([
            'message' => $count === 1
                ? 'Send record cleared. That user can receive another reminder.'
                : "{$count} send records cleared. Those users can receive another reminder.",
            'alert-type' => 'success',
        ]);
    }

    public function generateNow(InactiveUserNudgeService $service)
    {
        try {
            $result = $service->run('manual');
        } catch (\Throwable $e) {
            return redirect()->route('superadmin.inactive.nudge.index')->with([
                'message' => 'Could not send reminders: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        if ($result['eligible'] === 0) {
            return redirect()->route('superadmin.inactive.nudge.index')->with([
                'message' => 'No users match the current idle-days setting.',
                'alert-type' => 'info',
            ]);
        }

        return redirect()->route('superadmin.inactive.nudge.index')->with([
            'message' => "Reminders sent: {$result['sent']}. Failed: {$result['failed']}.",
            'alert-type' => $result['failed'] > 0 ? 'warning' : 'success',
        ]);
    }

    private function selectedUser(Request $request): User
    {
        $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->whereIn('role', ['student', 'lecturer'])),
            ],
        ]);

        return User::findOrFail($request->user_id);
    }
}
