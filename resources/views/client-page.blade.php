<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title ?? $page->slug }}</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.0/dist/tailwind.min.css" rel="stylesheet"> -->
      @vite('resources/css/app.css')
</head>
<body 
  style="background-color: {{ $theme->background_color ?? '#ffffff' }};
         color: {{ $theme->text_color ?? '#000000' }};
         background-image: url('{{ $background_url }}');
         background-size: cover;"
         
         >

    <div class="border rounded-lg overflow-hidden">
        {{-- Logo --}}
        @if ($logo_url)
            <div class="p-4 text-center">
                <img src="{{ $logo_url }}" alt="Logo" class="mx-auto h-20 mb-4">
            </div>
        @endif

        {{-- Hero Section --}}
        <div class="p-8 text-center" style="background-color: {{ $theme->primary_color ?? '#1a1a1a' }};">
            <h1 class="text-4xl font-bold mb-4">{{ $content['hero_title'] ?? 'Hero Title' }}</h1>
            <p class="text-xl">{{ $content['hero_subtitle'] ?? 'Hero Subtitle' }}</p>
        </div>

        {{-- About Section --}}
        <div class="p-8" style="background-color: {{ $theme->secondary_color ?? '#8b4513' }};">
            <h2 class="text-2xl font-bold mb-4">{{ $content['about_title'] ?? 'About' }}</h2>
            <p>{{ $content['about_text'] ?? 'About text goes here...' }}</p>
        </div>

        {{-- Contact Section --}}
        <div class="p-8">
            <h2 class="text-2xl font-bold mb-4" style="color: {{ $theme->accent_color ?? '#ffffff' }};">
                {{ $content['contact_title'] ?? 'Contact' }}
            </h2>
            <p>{{ $content['contact_text'] ?? 'Contact information...' }}</p>
        </div>
    </div>

</body>
</html>
