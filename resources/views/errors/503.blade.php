<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Maintenance — CCOD</title>
    <style>
        :root {
            --primary-50: #fffbeb;
            --primary-100: #fef3c7;
            --primary-200: #fde68a;
            --primary-600: #d97706;
            --primary-700: #b45309;
            --gray-600: #4b5563;
            --gray-900: #111827;
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
        .brand { margin-bottom: 52px; font-size: 28px; font-weight: 700; letter-spacing: -.02em; }
        .icon {
            width: 68px; height: 68px; margin: 0 auto 24px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--primary-200); border-radius: 12px;
            background: var(--primary-50); color: var(--primary-700);
        }
        .icon svg { width: 34px; height: 34px; }
        .code { margin: 0; font-size: 64px; line-height: 1; font-weight: 700; letter-spacing: -.04em; }
        h1 { margin: 16px 0 0; font-size: 24px; line-height: 32px; font-weight: 600; letter-spacing: -.015em; }
        p { margin: 12px auto 0; max-width: 520px; color: var(--gray-600); font-size: 15px; line-height: 24px; }
        .note { margin-top: 24px; padding: 12px 16px; border: 1px solid var(--primary-100); border-radius: 6px; background: var(--primary-50); color: var(--gray-600); font-size: 14px; line-height: 20px; }
        @media (max-width: 640px) {
            .page { padding: 32px 20px; }
            .brand { margin-bottom: 40px; }
            .code { font-size: 56px; }
            h1 { font-size: 22px; }
        }
        @media (prefers-color-scheme: dark) {
            body { background: #09090b; color: #f4f4f5; }
            .code, h1, .brand { color: #f4f4f5; }
            p { color: #a1a1aa; }
            .note { color: #a1a1aa; background: rgba(255,255,255,.04); }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="content" aria-labelledby="maintenance-title">
            <div class="brand">CCOD</div>
            <div class="icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3v3"/><path d="M12 18v3"/><path d="m4.22 4.22 2.12 2.12"/><path d="m17.66 17.66 2.12 2.12"/>
                    <path d="M3 12h3"/><path d="M18 12h3"/><path d="m4.22 19.78 2.12-2.12"/><path d="m17.66 6.34 2.12-2.12"/>
                    <circle cx="12" cy="12" r="3.5"/>
                </svg>
            </div>
            <p class="code">503</p>
            <h1 id="maintenance-title">We'll be back shortly</h1>
            <p>CCOD is temporarily unavailable while maintenance work is being carried out. Please try again later.</p>
            <div class="note">The application is currently in maintenance mode.</div>
        </section>
    </main>
</body>
</html>
