<!DOCTYPE html>
<html lang="{{ $page->language ?? 'en' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="canonical" href="{{ $page->getUrl() }}">
        <meta name="description" content="{{ $page->description }}">
        <title>@hasSection('title')@yield('title') · {{ $page->site_name }}@else{{ $page->site_name }}@endif</title>
        <link rel="stylesheet" href="/assets/css/main.css">
    </head>
    <body>
        <div class="wrap">
            @yield('body')
        </div>
    </body>
</html>
