<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlatformController extends Controller
{
    /**
     * Display a listing of platforms.
     */
    public function index()
    {
        $platforms = Platform::orderBy('name')->get();

        return Inertia::render('Admin/Platforms/Index', [
            'platforms' => $platforms,
        ]);
    }

    /**
     * Show the form for creating a new platform.
     */
    public function create()
    {
        return Inertia::render('Admin/Platforms/Create');
    }

    /**
     * Store a newly created platform in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:platforms,name',
            'slug' => 'required|string|unique:platforms,slug|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Platform::create($validated);

        return redirect()->route('platforms.index')->with('success', 'Platform created successfully');
    }

    /**
     * Show the form for editing the specified platform.
     */
    public function edit(Platform $platform)
    {
        return Inertia::render('Admin/Platforms/Edit', [
            'platform' => $platform,
        ]);
    }

    /**
     * Update the specified platform in storage.
     */
    public function update(Request $request, Platform $platform)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:platforms,name,' . $platform->id,
            'slug' => 'required|string|unique:platforms,slug,' . $platform->id . '|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $platform->update($validated);

        return redirect()->route('platforms.index')->with('success', 'Platform updated successfully');
    }

    /**
     * Toggle the active status of a platform.
     */
    public function toggle(Platform $platform)
    {
        $platform->update([
            'is_active' => !$platform->is_active,
        ]);

        return redirect()->route('platforms.index')->with('success', 'Platform status updated');
    }

    /**
     * Remove the specified platform from storage.
     */
    public function destroy(Platform $platform)
    {
        if ($platform->posts()->exists()) {
            return redirect()->route('platforms.index')->with('error', 'Cannot delete platform with existing posts');
        }

        $platform->delete();

        return redirect()->route('platforms.index')->with('success', 'Platform deleted successfully');
    }
}
