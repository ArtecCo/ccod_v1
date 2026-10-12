@php
    $logoLight = \App\Support\FilamentStyling::logoLight();
    $logoDark = \App\Support\FilamentStyling::logoDark() ?? $logoLight;
    $primary = \App\Support\FilamentStyling::colors()['primary'];
    $radius = \App\Support\FilamentStyling::cornerRadius();
    $brandName = \App\Support\FilamentStyling::brandName();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Maintenance — {{ $brandName }}</title>
    <style>
        :root {
            --primary-50: {{ $primary[50] }};
            --primary-100: {{ $primary[100] }};
            --primary-200: {{ $primary[200] }};
            --primary-500: {{ $primary[500] }};
            --primary-600: {{ $primary[600] }};
            --primary-700: {{ $primary[700] }};
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-900: #111827;
            --radius: {{ $radius }};
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #fff;
            color: var(--gray-900);
            -webkit-font-smoothing: antialiased;
        }
        .page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 48px 24px; }
        .content { width: min(100%, 620px); text-align: center; }
        .brand { display: flex; justify-content: center; margin-bottom: 52px; min-height: 58px; }
        .brand img { display: block; width: auto; max-width: 220px; max-height: 58px; object-fit: contain; }
        .icon {
            width: 68px; height: 68px; margin: 0 auto 24px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--primary-200); border-radius: calc(var(--radius) * 2);
            background: var(--primary-50); color: var(--primary-700);
        }
        .icon svg { width: 34px; height: 34px; }
        .code { margin: 0; font-size: 64px; line-height: 1; font-weight: 700; letter-spacing: -.04em; }
        h1 { margin: 16px 0 0; font-size: 24px; line-height: 32px; font-weight: 600; letter-spacing: -.015em; }
        p { margin: 12px auto 0; max-width: 520px; color: var(--gray-600); font-size: 15px; line-height: 24px; }
        .note { margin-top: 24px; padding: 12px 16px; border: 1px solid var(--primary-100); border-radius: var(--radius); background: var(--primary-50); color: var(--gray-600); font-size: 14px; line-height: 20px; }
        @media (max-width: 640px) {
            .page { padding: 32px 20px; }
            .brand { margin-bottom: 40px; }
            .brand img { max-width: 180px; max-height: 48px; }
            .code { font-size: 56px; }
            h1 { font-size: 22px; }
        }
        @media (prefers-color-scheme: dark) {
            body { background: #09090b; color: #f4f4f5; }
            .code, h1 { color: #f4f4f5; }
            p { color: #a1a1aa; }
            .note { color: #a1a1aa; border-color: var(--primary-800); background: rgba(255,255,255,.04); }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="content" aria-labelledby="maintenance-title">
            <div class="brand">
                @if ($logoLight)
                    <picture>
                        @if ($logoDark)
                            <source media="(prefers-color-scheme: dark)" srcset="{{ $logoDark }}">
                        @endif
                        <img src="{{ $logoLight }}" alt="{{ $brandName }}">
                    </picture>
                @endif
            </div>

            <div class="icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3v3"/><path d="M12 18v3"/><path d="m4.22 4.22 2.12 2.12"/><path d="m17.66 17.66 2.12 2.12"/>
                    <path d="M3 12h3"/><path d="M18 12h3"/><path d="m4.22 19.78 2.12-2.12"/><path d="m17.66 6.34 2.12-2.12"/>
                    <circle cx="12" cy="12" r="3.5"/>
                </svg>
            </div>

            <p class="code">503</p>
            <h1 id="maintenance-title">We'll be back shortly</h1>
            <p>
                CCOD is temporarily unavailable while maintenance work is being carried out.
                Please try again later.
            </p>
            <div class="note">The application is currently in maintenance mode.</div>
        </section>
    </main>
</body>
</html>
