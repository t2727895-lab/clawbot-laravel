<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Platform;
use App\Services\ClawbotService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    const STATUSES = ['pending_approval', 'queued', 'rejected', 'published', 'failed'];

    /**
     * Display a listing of the posts.
     */
    public function index()
    {
        $posts = Post::with('platforms')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(fn($post) => [
                'id' => $post->id,
                'content' => $post->content,
                'image' => $post->image,
                'status' => $post->status,
                'platforms' => $post->platforms->pluck('name')->toArray(),
                'scheduled_at' => $post->scheduled_at?->toIso8601String(),
                'scheduled_at_formatted' => $post->scheduled_at?->format('M d, Y H:i'),
                'published_at' => $post->published_at?->format('M d, Y H:i'),
                'created_at' => $post->created_at?->format('M d, Y'),
            ]);

        $activePlatforms = Platform::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('Admin/Posts/Index', [
            'posts' => $posts,
            'platforms' => $activePlatforms,
            'statuses' => self::STATUSES,
        ]);
    }

    /**
     * Handle image upload and return complete URL
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('posts', $filename, 'public');

        $url = asset('storage/' . $path);

        return response()->json(['url' => $url]);
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $activePlatforms = Platform::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('Admin/Posts/Create', [
            'platforms' => $activePlatforms,
            'statuses' => self::STATUSES,
        ]);
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|string|url',
            'status' => 'required|in:' . implode(',', self::STATUSES),
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'integer|exists:platforms,id',
            'scheduled_at' => 'nullable|date_format:Y-m-d H:i',
        ]);

        $post = Post::create([
            'content' => $validated['content'],
            'image' => $validated['image'] ?? null,
            'status' => $validated['status'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
        ]);

        // Attach platforms
        $post->platforms()->attach($validated['platforms']);

        return redirect()->route('posts.index')->with('success', 'Post created successfully');
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $activePlatforms = Platform::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('Admin/Posts/Edit', [
            'post' => [
                'id' => $post->id,
                'content' => $post->content ?? '',
                'image' => $post->image ?? '',
                'status' => $post->status,
                'platforms' => $post->platforms->pluck('id')->toArray(),
                'scheduled_at' => $post->scheduled_at?->format('Y-m-d\TH:i'),
            ],
            'platforms' => $activePlatforms,
            'statuses' => self::STATUSES,
        ]);
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|string|url',
            'status' => 'required|in:' . implode(',', self::STATUSES),
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'integer|exists:platforms,id',
            'scheduled_at' => 'nullable|date_format:Y-m-d H:i',
        ]);

        $post->update([
            'content' => $validated['content'],
            'image' => $validated['image'] ?? null,
            'status' => $validated['status'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
        ]);

        // Re-sync platforms
        $post->platforms()->sync($validated['platforms']);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully');
    }

    /**
     * Show detailed view of a post with all logs
     */
    public function show(Post $post)
    {
        $postData = [
            'id' => $post->id,
            'content' => $post->content,
            'image' => $post->image,
            'status' => $post->status,
            'platforms' => $post->platforms->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->toArray(),
            'scheduled_at' => $post->scheduled_at?->toIso8601String(),
            'scheduled_at_formatted' => $post->scheduled_at?->format('M d, Y H:i'),
            'published_at' => $post->published_at?->format('M d, Y H:i A'),
            'created_at' => $post->created_at?->format('M d, Y H:i A'),
            'approved_at' => $post->approved_at?->format('M d, Y H:i A'),
            'approved_by_name' => $post->approver?->name,
        ];

        $logs = $post->logs()->get()->map(fn($log) => [
            'id' => $log->id,
            'action' => $log->action,
            'status' => $log->status,
            'message' => $log->message,
            'response_data' => $log->response_data,
            'created_at' => $log->created_at?->format('M d, Y H:i:s A'),
            'platform_name' => $log->platform?->name,
        ])->toArray();

        return Inertia::render('Admin/Posts/Show', [
            'post' => $postData,
            'logs' => $logs,
            'statuses' => self::STATUSES,
        ]);
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post deleted successfully');
    }

    /**
     * Update post status with webhook support
     */
    public function updateStatus(Request $request, Post $post)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:' . implode(',', self::STATUSES),
                'call_webhook' => 'nullable|boolean',
                'scheduled_at' => 'nullable|string',
            ]);

            $newStatus = $validated['status'];
            $callWebhook = $validated['call_webhook'] ?? false;

            // If changing to queued, handle scheduled_at
            if ($newStatus === 'queued') {
                if (!empty($validated['scheduled_at'])) {
                    // Parse ISO datetime string
                    $post->scheduled_at = \Carbon\Carbon::parse($validated['scheduled_at']);
                } elseif (!$post->scheduled_at) {
                    // If no scheduled_at provided and post doesn't have one, set to 1 hour from now
                    $post->scheduled_at = now()->addHour();
                }
            }

            // If publishing, set published_at
            if ($newStatus === 'published') {
                $post->published_at = now();
            }

            $post->status = $newStatus;
            $post->save();

            // Call Clawbot service if webhook requested (for published status)
            if ($callWebhook && $newStatus === 'published') {
                $service = new ClawbotService();
                $result = $service->publish($post);
            }

            return response()->json([
                'success' => true,
                'message' => 'Post status updated successfully',
                'post' => $post,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
