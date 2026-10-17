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
        \App\Enums\AccessRequestStatus::Revoked => 'bg-primary-500 ring-primary-500/15',
        \App\Enums\AccessRequestStatus::Rejected => 'bg-danger-500 ring-danger-500/15',
        default => 'bg-gray-300 ring-gray-300/30 dark:bg-gray-600 dark:ring-gray-600/30',
    };
@endphp

<div class="space-y-5 py-2">
    {{-- Status rail: the connectors occupy their own row, so they never cross text. --}}
    <div class="flex items-center px-1" aria-hidden="true">
        <span class="h-4 w-4 shrink-0 rounded-full bg-primary-500 ring-4 ring-primary-500/15"></span>
        <span class="h-0.5 flex-1 bg-gray-950 dark:bg-white"></span>
        <span class="h-4 w-4 shrink-0 rounded-full bg-primary-500 ring-4 ring-primary-500/15"></span>
        <span class="h-0.5 flex-1 {{ $decided ? 'bg-gray-950 dark:bg-white' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
        <span class="h-4 w-4 shrink-0 rounded-full {{ $decisionDotClass }}"></span>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-white/10 dark:bg-white/[0.03]">
            <div class="font-semibold text-gray-950 dark:text-white">Submitted</div>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                <div>{{ $record->created_at?->format('d M Y, H:i:s') ?? '—' }}</div>
                <div class="mt-1">Request submitted by {{ $record->user?->name ?? 'the requester' }}.</div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-white/10 dark:bg-white/[0.03]">
            <div class="font-semibold text-gray-950 dark:text-white">Awaiting approval</div>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                @if ($approvers->isNotEmpty())
                    <div class="font-medium text-gray-700 dark:text-gray-300">Eligible approvers</div>
                    <ul class="mt-2 space-y-1 pl-4">
                        @foreach ($approvers as $approver)
                            <li>{{ $approver->name }} <span class="text-gray-400 dark:text-gray-500">({{ $approver->email }})</span></li>
                        @endforeach
                    </ul>
                @else
                    No eligible approvers found.
                @endif
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-white/10 dark:bg-white/[0.03]">
            <div class="font-semibold text-gray-950 dark:text-white">{{ $decisionLabel }}</div>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                @if ($record->decided_at)
                    <div>{{ $record->decided_at->format('d M Y, H:i:s') }}</div>
                    @if ($record->decided_by_id && $record->decided_by_type)
                        @php
                            $decisionMaker = $record->decided_by_type === \App\Models\Developer::class
                                ? \App\Models\Developer::query()->find($record->decided_by_id)
                                : \App\Models\User::query()->find($record->decided_by_id);
                        @endphp
                        @if ($decisionMaker)
                            <div class="mt-1">Decision by {{ $decisionMaker->name }}</div>
                        @endif
                    @endif
                    @if ($record->decision_reason)
                        <div class="mt-3 border-t border-gray-200 pt-3 text-gray-700 dark:border-white/10 dark:text-gray-300">
                            <span class="font-medium">Reason:</span> {{ $record->decision_reason }}
                        </div>
                    @endif
                @else
                    <div>Not decided yet.</div>
                    <div class="mt-1">The request is waiting for an eligible approver to take a decision.</div>
                @endif
            </div>
        </div>
    </div>
</div>
