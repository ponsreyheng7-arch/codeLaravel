<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        // Get authenticated user
        $user = auth()->user(); 
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Validate input
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'address' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $profileImagePath = $user->profile_image; // default old image

        // Handle new image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_images', $filename, 'public');

            // Delete old image if exists
            if (!empty($user->profile_image) && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $profileImagePath = $path;
        }

        // Update the user in DB manually
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'name' => $validated['name'] ?? $user->name,
                'email' => $validated['email'] ?? $user->email,
                'phone' => $validated['phone'] ?? $user->phone,
                'address' => $validated['address'] ?? $user->address,
                'profile_image' => $profileImagePath,
            ]);

        // Fetch updated user
        $updatedUser = DB::table('users')->where('id', $user->id)->first();

        // Build full image URL
        $profileUrl = $updatedUser->profile_image && Storage::disk('public')->exists($updatedUser->profile_image)
            ? Storage::url($updatedUser->profile_image)
            : null;

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $updatedUser->id,
                'name' => $updatedUser->name,
                'email' => $updatedUser->email,
                'phone' => $updatedUser->phone,
                'address' => $updatedUser->address,
                'profile_image' => $updatedUser->profile_image,
                'profile_image_url' => $profileUrl,
            ],
        ]);
    }
    public function profile(Request $request)
{
    $user = auth()->user();
    return response()->json([
        'user' => $user
    ]);
}

}
