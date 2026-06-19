<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Artisan;


Route::get('/publish-due-posts', function () {
    Artisan::call('posts:publish-due');

    return response()->json([
        'success' => true,
        'message' => 'posts:publish-due command executed',
        'output' => Artisan::output(),
    ]);
});

Route::get('/check-env', function () {
    return [
        'url' => env('CLAWBOT_BASE_URL'),
        'config' => config('app.url')
    ];
});

Route::get('/test-clawbot', function () {
    $baseUrl = config('services.clawbot.base_url');
    $apiKey = config('services.clawbot.api_key');

    if (!$baseUrl || !$apiKey) {
        return response()->json([
            'success' => false,
            'message' => 'Clawbot credentials not configured',
            'base_url' => $baseUrl,
            'api_key_exists' => !empty($apiKey),
        ], 400);
    }

    try {
        $url = $baseUrl . '/v1/chat/completions';

        $data = [
            'model' => 'openclaw',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Hello! This is a test message to verify the connection to OpenClaw.'
                ]
            ],
            'stream' => false
        ];

        $payload = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        return response()->json([
            'success' => $http_code === 200 && !$curl_error,
            'http_code' => $http_code,
            'curl_error' => $curl_error,
            'response' => $responseData,
            'base_url' => $baseUrl,
            'api_key_configured' => !empty($apiKey),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'base_url' => $baseUrl,
            'api_key_exists' => !empty($apiKey),
        ], 500);
    }
});

Route::get('/', function () {
    // Redirect to login if not authenticated, otherwise to dashboard
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class, ['names' => 'users']);
});

Route::get('/users', [UserController::class, 'index'])->middleware(['auth', 'verified'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->middleware(['auth', 'verified'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->middleware(['auth', 'verified'])->name('users.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware(['auth', 'verified'])->name('users.edit');
Route::patch('/users/{user}', [UserController::class, 'update'])->middleware(['auth', 'verified'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware(['auth', 'verified'])->name('users.destroy');

// Posts Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::post('/upload-image', [PostController::class, 'uploadImage'])->name('upload-image');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::patch('/posts/{post}/status', [PostController::class, 'updateStatus'])->name('posts.updateStatus');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});

// Platforms Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/platforms', [\App\Http\Controllers\PlatformController::class, 'index'])->name('platforms.index');
    Route::get('/platforms/create', [\App\Http\Controllers\PlatformController::class, 'create'])->name('platforms.create');
    Route::post('/platforms', [\App\Http\Controllers\PlatformController::class, 'store'])->name('platforms.store');
    Route::get('/platforms/{platform}/edit', [\App\Http\Controllers\PlatformController::class, 'edit'])->name('platforms.edit');
    Route::patch('/platforms/{platform}', [\App\Http\Controllers\PlatformController::class, 'update'])->name('platforms.update');
    Route::post('/platforms/{platform}/toggle', [\App\Http\Controllers\PlatformController::class, 'toggle'])->name('platforms.toggle');
    Route::delete('/platforms/{platform}', [\App\Http\Controllers\PlatformController::class, 'destroy'])->name('platforms.destroy');
});

require __DIR__.'/auth.php';
