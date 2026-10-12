<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>403 — Access Denied</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full bg-white text-gray-900 antialiased dark:bg-gray-950 dark:text-white">
    <main class="flex min-h-screen flex-col items-center justify-center px-6 py-12 text-center">
        <div class="w-full max-w-2xl">
            <div class="mb-10 flex justify-center">
                <img src="{{ asset('images/bosch-logo.svg') }}" alt="Bosch" class="h-12 w-auto object-contain dark:brightness-0 dark:invert">
            </div>

            <div class="mx-auto mb-7 flex h-28 w-28 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-900" aria-hidden="true">
                <svg viewBox="0 0 96 96" class="h-16 w-16 text-gray-700 dark:text-gray-200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="48" cy="48" r="34" stroke="currentColor" stroke-width="5"/>
                    <path d="M34 34L62 62M62 34L34 62" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
                </svg>
            </div>

            <p class="text-7xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-8xl">403</p>
            <h1 class="mt-5 text-2xl font-semibold tracking-tight sm:text-3xl">Access denied</h1>
            <p class="mx-auto mt-4 max-w-xl text-base leading-7 text-gray-600 dark:text-gray-400 sm:text-lg">
                You don't seem to have the required permissions to view this page.
                Please contact the admin to raise an access request.
            </p>

            <div class="mt-9 flex flex-wrap justify-center gap-3">
                <button type="button" onclick="history.back()" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-900">
                    Go back
                </button>
                <a href="{{ url('/') }}" class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                    Go to dashboard
                </a>
            </div>
        </div>
    </main>
</body>
</html>
