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
    <title>404 — Page Not Found</title>
    <style>
        :root {
            --primary-50: {{ $primary[50] }};
            --primary-100: {{ $primary[100] }};
            --primary-200: {{ $primary[200] }};
            --primary-500: {{ $primary[500] }};
            --primary-600: {{ $primary[600] }};
            --primary-700: {{ $primary[700] }};
            --primary-800: {{ $primary[800] }};
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
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

        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
        }

        .error-content {
            width: min(100%, 640px);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 58px;
            margin-bottom: 52px;
        }

        .brand img {
            display: block;
            width: auto;
            max-width: 220px;
            max-height: 58px;
            object-fit: contain;
        }

        .status-icon {
            width: 68px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            border: 1px solid var(--primary-200);
            border-radius: calc(var(--radius) * 2);
            background: var(--primary-50);
            color: var(--primary-700);
        }

        .status-icon svg { width: 34px; height: 34px; }

        .code {
            margin: 0;
            font-size: 64px;
            line-height: 1;
            font-weight: 700;
            letter-spacing: -0.04em;
            color: var(--gray-900);
        }

        h1 {
            margin: 16px 0 0;
            font-size: 24px;
            line-height: 32px;
            font-weight: 600;
            letter-spacing: -0.015em;
        }

        .message {
            max-width: 540px;
            margin: 12px 0 0;
            color: var(--gray-600);
            font-size: 15px;
            line-height: 24px;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 28px;
        }

        .button {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 16px;
            border-radius: var(--radius);
            border: 1px solid transparent;
            font-size: 14px;
            line-height: 20px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease, box-shadow .15s ease;
        }

        .button-secondary {
            background: #fff;
            border-color: var(--gray-200);
            color: var(--gray-700);
        }

        .button-secondary:hover { background: var(--gray-50); }

        .button-primary {
            background: var(--primary-600);
            color: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,.08);
        }

        .button-primary:hover { background: var(--primary-700); }
        .button:focus-visible {
            outline: 2px solid var(--primary-500);
            outline-offset: 2px;
        }

        @media (max-width: 640px) {
            .error-page { padding: 32px 20px; }
            .brand { margin-bottom: 40px; }
            .brand img { max-width: 180px; max-height: 48px; }
            .code { font-size: 56px; }
            h1 { font-size: 22px; }
            .actions { flex-direction: column-reverse; width: 100%; }
            .button { width: 100%; }
        }

        @media (prefers-color-scheme: dark) {
            body { background: #09090b; color: #f4f4f5; }
            .code, h1 { color: #f4f4f5; }
            .message { color: #a1a1aa; }
            .button-secondary { background: #18181b; border-color: #3f3f46; color: #e4e4e7; }
            .button-secondary:hover { background: #27272a; }
        }
    </style>
</head>
<body>
    <main class="error-page">
        <section class="error-content" aria-labelledby="error-title">
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

            <div class="status-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 3.75h8.25L19 8.5v11.75H6V3.75Z"/>
                    <path d="M14 3.75V9h5"/>
                    <path d="m9 13 2 2 4-4"/>
                </svg>
            </div>

            <p class="code">404</p>
            <h1 id="error-title">Oops, we couldn't find this page</h1>
            <p class="message">The page you're looking for doesn't exist or may have been moved.</p>

            <div class="actions">
                <button type="button" onclick="history.back()" class="button button-secondary">Go back</button>
                <a href="{{ url('/') }}" class="button button-primary">Go to dashboard</a>
            </div>
        </section>
    </main>
</body>
</html>
