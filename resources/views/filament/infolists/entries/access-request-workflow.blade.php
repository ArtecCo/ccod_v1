@php
    $record = $getRecord();
    $authorization = app(\App\Services\AccessAuthorizationService::class);
    $approvers = $authorization->approvers($record);
    $status = $record->status instanceof \App\Enums\AccessRequestStatus
        ? $record->status
        : \App\Enums\AccessRequestStatus::tryFrom((string) $record->status);
    $decided = in_array($status, [
        \App\Enums\AccessRequestStatus::Approved,
        \App\Enums\AccessRequestStatus::Rejected,
        \App\Enums\AccessRequestStatus::Revoked,
    ], true);
    $decisionLabel = match ($status) {
        \App\Enums\AccessRequestStatus::Approved,
        \App\Enums\AccessRequestStatus::Revoked => 'Approved',
        \App\Enums\AccessRequestStatus::Rejected => 'Rejected',
        default => 'Not decided',
    };
    $decisionDotClass = match ($status) {
        \App\Enums\AccessRequestStatus::Approved,
        \App\Enums\AccessRequestStatus::Revoked => 'bg-primary-500',
        \App\Enums\AccessRequestStatus::Rejected => 'bg-danger-500',
        default => 'bg-gray-300 dark:bg-gray-600',
    };
    $decisionRingClass = match ($status) {
        \App\Enums\AccessRequestStatus::Approved,
        \App\Enums\AccessRequestStatus::Revoked => 'ring-primary-500/15',
        \App\Enums\AccessRequestStatus::Rejected => 'ring-danger-500/15',
        default => 'ring-gray-300/30 dark:ring-gray-600/30',
    };
@endphp

<div class="overflow-x-auto py-2">
    <div class="relative min-w-[760px] px-4">
        <div class="pointer-events-none absolute left-[16.666%] right-[50%] top-4 h-0.5 bg-gray-950 dark:bg-white"></div>
        <div class="pointer-events-none absolute left-[50%] right-[16.666%] top-4 h-0.5 {{ $decided ? 'bg-gray-950 dark:bg-white' : 'bg-gray-300 dark:bg-gray-600' }}"></div>

        <div class="grid grid-cols-3 gap-8">
            <div class="relative text-left">
                <div class="relative z-10 flex items-center gap-2">
                    <span class="h-4 w-4 shrink-0 rounded-full bg-primary-500 ring-4 ring-primary-500/15"></span>
                    <span class="font-semibold text-gray-950 dark:text-white">Submitted</span>
                </div>
                <div class="mt-2 pl-6 text-sm text-gray-500 dark:text-gray-400">
                    {{ $record->created_at?->format('d M Y, H:i:s') ?? '—' }}
                </div>
            </div>

            <div class="relative text-left">
                <div class="relative z-10 flex items-center gap-2">
                    <span class="h-4 w-4 shrink-0 rounded-full bg-primary-500 ring-4 ring-primary-500/15"></span>
                    <span class="font-semibold text-gray-950 dark:text-white">Awaiting approval</span>
                </div>
                <div class="mt-2 pl-6 text-sm text-gray-500 dark:text-gray-400">
                    @if ($approvers->isNotEmpty())
                        <div class="mb-1">Eligible approvers</div>
                        <ul class="m-0 list-disc pl-4">
                            @foreach ($approvers as $approver)
                                <li>{{ $approver->name }} <span class="opacity-75">({{ $approver->email }})</span></li>
                            @endforeach
                        </ul>
                    @else
                        No eligible approvers found.
                    @endif
                </div>
            </div>

            <div class="relative text-left">
                <div class="relative z-10 flex items-center gap-2">
                    <span class="h-4 w-4 shrink-0 rounded-full {{ $decisionDotClass }} ring-4 {{ $decisionRingClass }}"></span>
                    <span class="font-semibold text-gray-950 dark:text-white">{{ $decisionLabel }}</span>
                </div>
                <div class="mt-2 pl-6 text-sm text-gray-500 dark:text-gray-400">
                    @if ($record->decided_at)
                        <div>{{ $record->decided_at->format('d M Y, H:i:s') }}</div>
                        @if ($record->decided_by_id && $record->decided_by_type)
                            @php
                                $decisionMaker = $record->decided_by_type === \App\Models\Developer::class
                                    ? \App\Models\Developer::query()->find($record->decided_by_id)
                                    : \App\Models\User::query()->find($record->decided_by_id);
                            @endphp
                            @if ($decisionMaker)
                                <div class="mt-1">By: {{ $decisionMaker->name }}</div>
                            @endif
                        @endif
                        @if ($record->decision_reason)
                            <div class="mt-2 text-gray-700 dark:text-gray-300">Reason: {{ $record->decision_reason }}</div>
                        @endif
                    @else
                        Not decided yet.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
