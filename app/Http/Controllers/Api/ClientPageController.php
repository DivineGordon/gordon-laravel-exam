<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientPageController extends Controller
{
    public function show(Request $request)
    {
        $page = $request->user()->clientPage()->with('theme')->first();

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return response()->json($page);
    }

    public function update(Request $request)
    {
        $page = $request->user()->clientPage;

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        $request->validate([
            'content' => 'sometimes|array',
            'theme_id' => 'sometimes|exists:page_themes,id',
            'is_published' => 'sometimes|boolean',
        ]);

        $page->update($request->only(['content', 'theme_id', 'is_published']));

        return response()->json($page->load('theme'));
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $page = $request->user()->clientPage;

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        // Delete old logo if exists
        if ($page->logo_path) {
            Storage::disk('public')->delete($page->logo_path);
        }

        // Store new logo
        $path = $request->file('logo')->store('logos', 'public');
        $page->update(['logo_path' => $path]);

        return response()->json([
            'message' => 'Logo uploaded successfully',
            'logo_url' => Storage::url($path),
        ]);
    }

    public function uploadBackground(Request $request)
    {
        $request->validate([
            'background' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $page = $request->user()->clientPage;

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        // Delete old background if exists
        if ($page->background_image_path) {
            Storage::disk('public')->delete($page->background_image_path);
        }

        // Store new background
        $path = $request->file('background')->store('backgrounds', 'public');
        $page->update(['background_image_path' => $path]);

        return response()->json([
            'message' => 'Background uploaded successfully',
            'background_url' => Storage::url($path),
        ]);
    }
}