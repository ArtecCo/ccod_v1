@php
    $record = $getRecord();
    $authorization = app(\App\Services\AccessAuthorizationService::class);
    $approvers = $authorization->approvers($record);
    $status = $record->status instanceof \App\Enums\AccessRequestStatus
        ? $record->status
        : \App\Enums\AccessRequestStatus::tryFrom((string) $record->status);
    $decisionLabel = match ($status) {
        \App\Enums\AccessRequestStatus::Approved,
        \App\Enums\AccessRequestStatus::Revoked => 'Approved',
        \App\Enums\AccessRequestStatus::Rejected => 'Rejected',
        default => 'Not decided',
    };
    $decisionClass = match ($status) {
        \App\Enums\AccessRequestStatus::Approved,
        \App\Enums\AccessRequestStatus::Revoked => 'success',
        \App\Enums\AccessRequestStatus::Rejected => 'danger',
        default => 'pending',
    };
@endphp

<div style="overflow-x:auto; padding:0.5rem 0 0.75rem;">
    <div style="min-width:760px; display:grid; grid-template-columns:minmax(190px,1fr) 56px minmax(250px,1.25fr) 56px minmax(190px,1fr); align-items:start;">
        <div>
            <div style="display:flex; align-items:center; gap:0.65rem;">
                <span style="width:1rem; height:1rem; flex:0 0 1rem; border-radius:9999px; background:#10b981; box-shadow:0 0 0 4px rgba(16,185,129,.12);"></span>
                <span style="font-weight:600;">Submitted</span>
            </div>
            <div style="margin:0.55rem 0 0 1.65rem; color:#6b7280; font-size:.875rem;">
                {{ $record->created_at?->format('d M Y, H:i:s') ?? '—' }}
            </div>
        </div>

        <div style="height:2px; margin-top:0.45rem; background:#10b981;"></div>

        <div>
            <div style="display:flex; align-items:center; gap:0.65rem;">
                <span style="width:1rem; height:1rem; flex:0 0 1rem; border-radius:9999px; background:{{ $status === \App\Enums\AccessRequestStatus::Pending ? '#f59e0b' : '#10b981' }}; box-shadow:0 0 0 4px {{ $status === \App\Enums\AccessRequestStatus::Pending ? 'rgba(245,158,11,.12)' : 'rgba(16,185,129,.12)' }};"></span>
                <span style="font-weight:600;">Awaiting approval</span>
            </div>
            <div style="margin:0.55rem 0 0 1.65rem; color:#6b7280; font-size:.875rem;">
                @if ($approvers->isNotEmpty())
                    <div style="margin-bottom:.3rem;">Eligible approvers</div>
                    <ul style="margin:0; padding-left:1rem;">
                        @foreach ($approvers as $approver)
                            <li>{{ $approver->name }} <span style="opacity:.75;">({{ $approver->email }})</span></li>
                        @endforeach
                    </ul>
                @else
                    No eligible approvers found.
                @endif
            </div>
        </div>

        <div style="height:2px; margin-top:0.45rem; background:{{ $status === \App\Enums\AccessRequestStatus::Pending ? '#d1d5db' : '#10b981' }};"></div>

        <div>
            <div style="display:flex; align-items:center; gap:0.65rem;">
                <span style="width:1rem; height:1rem; flex:0 0 1rem; border-radius:9999px; background:{{ $decisionClass === 'success' ? '#10b981' : ($decisionClass === 'danger' ? '#ef4444' : '#d1d5db') }}; box-shadow:0 0 0 4px {{ $decisionClass === 'success' ? 'rgba(16,185,129,.12)' : ($decisionClass === 'danger' ? 'rgba(239,68,68,.12)' : 'rgba(209,213,219,.35)') }};"></span>
                <span style="font-weight:600;">{{ $decisionLabel }}</span>
            </div>
            <div style="margin:0.55rem 0 0 1.65rem; color:#6b7280; font-size:.875rem;">
                @if ($record->decided_at)
                    <div>{{ $record->decided_at->format('d M Y, H:i:s') }}</div>
                    @if ($record->decided_by_id && $record->decided_by_type)
                        @php
                            $decisionMaker = $record->decided_by_type === \App\Models\Developer::class
                                ? \App\Models\Developer::query()->find($record->decided_by_id)
                                : \App\Models\User::query()->find($record->decided_by_id);
                        @endphp
                        @if ($decisionMaker)
                            <div style="margin-top:.25rem;">By: {{ $decisionMaker->name }}</div>
                        @endif
                    @endif
                    @if ($record->decision_reason)
                        <div style="margin-top:.45rem; color:#374151;">Reason: {{ $record->decision_reason }}</div>
                    @endif
                @else
                    Not decided yet.
                @endif
            </div>
        </div>
    </div>
</div>
