<?php

namespace App\Http\Controllers;

use App\Models\ClientPage;
use App\Models\PageAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicPageController extends Controller
{
    public function show(Request $request, $slug)
    {
        $page = ClientPage::where('slug', $slug)
            ->where('is_published', true)
            ->with('theme')
            ->firstOrFail();

        // Track analytics
        $sessionId = $request->session()->getId();
        
        PageAnalytic::create([
            'client_page_id' => $page->id,
            'visitor_ip' => $request->ip(),
            'session_id' => $sessionId,
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'visited_at' => now(),
        ]);

           // Extract data for the view
        $theme = $page->theme;
        $content = $page->content ?? [];
        $logo_url = $page->logo_path ? asset('storage/' . $page->logo_path) : null;
        $background_url = $page->background_image_path ? asset('storage/' . $page->background_image_path) : null;

        // Return HTML view instead of JSON
        return view('client-page', compact('page', 'theme', 'content', 'logo_url', 'background_url'));

        // return response()->json([
        //     'page' => $page,
        //     'logo_url' => $page->logo_path ? asset('storage/' . $page->logo_path) : null,
        //     'background_url' => $page->background_image_path ? asset('storage/' . $page->background_image_path) : null,
        // ]);
    }
}