<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Services\TemplateSuggestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateMatchController extends Controller
{
    public function match(Request $request, TemplateSuggestionService $service)
    {
        $validated = $request->validate([
            'need' => 'required|string|min:3|max:500',
        ]);

        $user = Auth::user();
        $query = Template::query()->select('id', 'title', 'description', 'category');

        if (in_array($user->role, ['student', 'lecturer'], true)) {
            $query->where('is_active', 1)->where('category', ucfirst($user->role));
        }

        $templates = $query->orderBy('title')->limit(40)->get();

        try {
            $matches = $service->matchNeed($validated['need'], $templates);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $routeName = match ($user->role) {
            'admin' => 'details.template',
            'superadmin' => 'superadmin.details.template',
            default => 'user.details.template',
        };

        return response()->json([
            'success' => true,
            'matches' => collect($matches)->map(fn (array $match) => [
                'title' => $match['title'],
                'description' => $match['description'],
                'category' => $match['category'],
                'reason' => $match['reason'],
                'url' => route($routeName, $match['id']),
            ])->values(),
        ]);
    }
}
