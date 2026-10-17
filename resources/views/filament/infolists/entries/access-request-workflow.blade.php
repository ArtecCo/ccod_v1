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
    $decisionColor = match ($status) {
        \App\Enums\AccessRequestStatus::Approved,
        \App\Enums\AccessRequestStatus::Revoked => '#f59e0b',
        \App\Enums\AccessRequestStatus::Rejected => '#ef4444',
        default => '#d1d5db',
    };
@endphp

<div style="padding: 2px 0 0; font-size:13px; line-height:19px;">
    <div style="display:flex; align-items:center; width:100%; padding:0 4px; box-sizing:border-box;">
        <span style="display:block; width:11px; height:11px; min-width:11px; border-radius:50%; background:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,.14);"></span>
        <span style="display:block; height:1px; flex:1; background:#1f2937;"></span>
        <span style="display:block; width:11px; height:11px; min-width:11px; border-radius:50%; background:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,.14);"></span>
        <span style="display:block; height:1px; flex:1; background:{{ $decided ? '#1f2937' : '#d1d5db' }};"></span>
        <span style="display:block; width:11px; height:11px; min-width:11px; border-radius:50%; background:{{ $decisionColor }}; box-shadow:0 0 0 3px rgba(156,163,175,.14);"></span>
    </div>

    <div style="display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:24px; margin-top:12px;">
        <div style="padding:0 4px; min-width:0;">
            <div style="font-size:13px; font-weight:600; line-height:18px; color:#111827;">Submitted</div>
            <div style="margin-top:3px; color:#6b7280;">
                <div>{{ $record->created_at?->format('d M Y, H:i:s') ?? '—' }}</div>
                <div>{{ $record->user?->name ?? 'The requester' }}</div>
            </div>
        </div>

        <div style="padding:0 4px; min-width:0;">
            <div style="font-size:13px; font-weight:600; line-height:18px; color:#111827;">Awaiting approval</div>
            <div style="margin-top:3px; color:#6b7280;">
                @if ($approvers->isNotEmpty())
                    <div style="color:#4b5563;">Eligible approvers</div>
                    @foreach ($approvers as $approver)
                        <div>{{ $approver->name }} <span style="color:#9ca3af;">({{ $approver->email }})</span></div>
                    @endforeach
                @else
                    <div>No eligible approvers found.</div>
                @endif
            </div>
        </div>

        <div style="padding:0 4px; min-width:0;">
            <div style="font-size:13px; font-weight:600; line-height:18px; color:#111827;">{{ $decisionLabel }}</div>
            <div style="margin-top:3px; color:#6b7280;">
                @if ($record->decided_at)
                    <div>{{ $record->decided_at->format('d M Y, H:i:s') }}</div>
                    @if ($record->decided_by_id && $record->decided_by_type)
                        @php
                            $decisionMaker = $record->decided_by_type === \App\Models\Developer::class
                                ? \App\Models\Developer::query()->find($record->decided_by_id)
                                : \App\Models\User::query()->find($record->decided_by_id);
                        @endphp
                        @if ($decisionMaker)
                            <div>Decision by {{ $decisionMaker->name }}</div>
                        @endif
                    @endif
                    @if ($record->decision_reason)
                        <div style="margin-top:5px; color:#4b5563;"><span style="font-weight:500;">Reason:</span> {{ $record->decision_reason }}</div>
                    @endif
                @else
                    <div>Not decided yet.</div>
                    <div>The request is waiting for an eligible approver.</div>
                @endif
            </div>
        </div>
    </div>
</div>
