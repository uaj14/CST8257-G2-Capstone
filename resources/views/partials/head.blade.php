<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="Multitasker helps you organize tasks into focused lists, set priorities and due dates, and adjust your plan as work changes." />
<meta name="theme-color" content="#4f46e5" />
<meta property="og:title" content="{{ filled($title ?? null) ? $title.' - '.config('app.name', 'Multitasker') : config('app.name', 'Multitasker') }}" />
<meta property="og:description" content="Turn scattered tasks into a clear plan with Multitasker." />
<meta property="og:type" content="website" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Multitasker') : config('app.name', 'Multitasker') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
