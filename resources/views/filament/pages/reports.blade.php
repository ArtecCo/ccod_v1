<x-filament-panels::page>
    <div class="mx-auto w-full max-w-7xl space-y-7">
        <div class="flex flex-col gap-2 border-b border-gray-200 pb-5 dark:border-gray-800 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">Reports</h1>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Build downloadable PDF reports for a subscription, project, or business unit.
                </p>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Private downloads · Available for 7 days</div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(280px,0.8fr)]">
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-6">
                <div class="mb-5">
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Configure a report</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose the scope, reporting period, and PDF content.</p>
                </div>

                <form wire:submit="generate" class="space-y-5">
                    {{ $this->form }}
                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">
                            Generation runs in the background. You can safely leave this page.
                        </p>
                        <x-filament::button type="submit" icon="heroicon-o-document-arrow-down">
                            Generate PDF
                        </x-filament::button>
                    </div>
                </form>
            </section>

            <aside class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-gray-900/60 sm:p-6">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">How report output works</h2>
                <div class="mt-4 space-y-4 text-sm leading-6 text-gray-600 dark:text-gray-300">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Individual reports</p>
                        <p class="mt-1">Select one or more report sections. Each selected section is delivered as its own PDF.</p>
                    </div>
                    <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                        <p class="font-medium text-gray-900 dark:text-white">Consolidated PDF</p>
                        <p class="mt-1">Select Consolidated PDF on its own to include every available section, or select it with individual sections to consolidate only those sections.</p>
                    </div>
                    <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                        <p class="font-medium text-gray-900 dark:text-white">Access and retention</p>
                        <p class="mt-1">Only your developer account can download these files. Generated PDFs are automatically removed after seven days.</p>
                    </div>
                </div>
            </aside>
        </div>

        <div wire:poll.5s>
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Your generated reports</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The list updates automatically as background jobs finish.</p>
                </div>

                @if ($reports->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto flex size-11 items-center justify-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-document-text" class="size-5" />
                        </div>
                        <p class="mt-3 text-sm font-medium text-gray-900 dark:text-white">No reports generated yet</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your completed PDF exports will appear here.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($reports as $report)
                            <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $report->scope_name }}</p>
                                        <x-filament::badge :color="match($report->status) { 'completed' => 'success', 'failed' => 'danger', 'processing' => 'info', default => 'gray' }">
                                            {{ ucfirst($report->status) }}
                                        </x-filament::badge>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ ucfirst(str_replace('_', ' ', $report->scope_type)) }}
                                        <span class="mx-1">·</span>
                                        {{ $report->period_start->format('d M Y') }} – {{ $report->period_end->format('d M Y') }}
                                        <span class="mx-1">·</span>
                                        Requested {{ $report->created_at->format('d M Y, H:i') }}
                                    </p>
                                    @if ($report->failure_reason)
                                        <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $report->failure_reason }}</p>
                                    @endif
                                </div>
                                @if (!empty($report->files))
                                    <div class="flex shrink-0 flex-wrap gap-2">
                                        @foreach ($report->files as $file)
                                            <x-filament::button
                                                size="sm"
                                                color="gray"
                                                outlined
                                                tag="a"
                                                :href="route('developer.reports.download', ['report' => $report, 'file' => $file['key']])"
                                            >
                                                {{ $file['label'] }}
                                            </x-filament::button>
                                        @endforeach
                                    </div>
                                @elseif ($report->status === 'queued' || $report->status === 'processing')
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Preparing PDF files…</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-filament-panels::page>
