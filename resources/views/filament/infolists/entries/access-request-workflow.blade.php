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
        default => '#9ca3af',
    };
    $decisionRing = match ($status) {
        \App\Enums\AccessRequestStatus::Approved,
        \App\Enums\AccessRequestStatus::Revoked => 'rgba(245,158,11,.18)',
        \App\Enums\AccessRequestStatus::Rejected => 'rgba(239,68,68,.18)',
        default => 'rgba(156,163,175,.22)',
    };
@endphp

<div style="padding: 8px 4px 4px;">
    <div style="display:flex; align-items:center; width:100%; padding:0 8px; box-sizing:border-box;">
        <span style="display:block; width:16px; height:16px; min-width:16px; border-radius:50%; background:#f59e0b; box-shadow:0 0 0 5px rgba(245,158,11,.18);"></span>
        <span style="display:block; height:2px; flex:1; background:#111827;"></span>
        <span style="display:block; width:16px; height:16px; min-width:16px; border-radius:50%; background:#f59e0b; box-shadow:0 0 0 5px rgba(245,158,11,.18);"></span>
        <span style="display:block; height:2px; flex:1; background:{{ $decided ? '#111827' : '#d1d5db' }};"></span>
        <span style="display:block; width:16px; height:16px; min-width:16px; border-radius:50%; background:{{ $decisionColor }}; box-shadow:0 0 0 5px {{ $decisionRing }};"></span>
    </div>

    <div style="display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:20px; margin-top:18px;">
        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:16px; background:#f9fafb; min-height:96px; box-sizing:border-box;">
            <div style="font-size:14px; font-weight:600; line-height:20px; color:#111827;">Submitted</div>
            <div style="margin-top:7px; font-size:13px; line-height:19px; color:#6b7280;">
                <div>{{ $record->created_at?->format('d M Y, H:i:s') ?? '—' }}</div>
                <div style="margin-top:3px;">Request submitted by {{ $record->user?->name ?? 'the requester' }}.</div>
            </div>
        </div>

        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:16px; background:#f9fafb; min-height:96px; box-sizing:border-box;">
            <div style="font-size:14px; font-weight:600; line-height:20px; color:#111827;">Awaiting approval</div>
            <div style="margin-top:7px; font-size:13px; line-height:19px; color:#6b7280;">
                @if ($approvers->isNotEmpty())
                    <div style="font-weight:600; color:#374151;">Eligible approvers</div>
                    <div style="margin-top:5px;">
                        @foreach ($approvers as $approver)
                            <div style="margin-top:3px;">{{ $approver->name }} <span style="color:#9ca3af;">({{ $approver->email }})</span></div>
                        @endforeach
                    </div>
                @else
                    No eligible approvers found.
                @endif
            </div>
        </div>

        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:16px; background:#f9fafb; min-height:96px; box-sizing:border-box;">
            <div style="font-size:14px; font-weight:600; line-height:20px; color:#111827;">{{ $decisionLabel }}</div>
            <div style="margin-top:7px; font-size:13px; line-height:19px; color:#6b7280;">
                @if ($record->decided_at)
                    <div>{{ $record->decided_at->format('d M Y, H:i:s') }}</div>
                    @if ($record->decided_by_id && $record->decided_by_type)
                        @php
                            $decisionMaker = $record->decided_by_type === \App\Models\Developer::class
                                ? \App\Models\Developer::query()->find($record->decided_by_id)
                                : \App\Models\User::query()->find($record->decided_by_id);
                        @endphp
                        @if ($decisionMaker)
                            <div style="margin-top:3px;">Decision by {{ $decisionMaker->name }}</div>
                        @endif
                    @endif
                    @if ($record->decision_reason)
                        <div style="margin-top:9px; padding-top:8px; border-top:1px solid #e5e7eb; color:#374151;"><strong>Reason:</strong> {{ $record->decision_reason }}</div>
                    @endif
                @else
                    <div>Not decided yet.</div>
                    <div style="margin-top:3px;">The request is waiting for an eligible approver to take a decision.</div>
                @endif
            </div>
        </div>
    </div>
</div>
