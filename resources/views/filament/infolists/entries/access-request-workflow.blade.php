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
@endphp

<div style="width:100%; overflow-x:auto; padding:8px 0 12px;">
    <div style="position:relative; min-width:760px; padding:0 8px;">
        <div style="position:absolute; left:calc(16.6667% + 8px); right:calc(50% - 8px); top:8px; height:2px; background:#111827;"></div>
        <div style="position:absolute; left:calc(50% - 8px); right:calc(16.6667% + 8px); top:8px; height:2px; background:{{ $decided ? '#111827' : '#d1d5db' }};"></div>

        <div style="display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:32px; position:relative;">
            <div style="min-width:0;">
                <div style="display:flex; align-items:center; gap:8px; position:relative; z-index:1;">
                    <span style="display:block; width:16px; height:16px; flex:0 0 16px; border-radius:9999px; background:var(--primary-500,#f59e0b); box-shadow:0 0 0 4px color-mix(in srgb, var(--primary-500,#f59e0b) 15%, transparent);"></span>
                    <span style="font-weight:600; color:rgb(17 24 39);">Submitted</span>
                </div>
                <div style="margin:8px 0 0 24px; font-size:14px; color:rgb(107 114 128);">
                    {{ $record->created_at?->format('d M Y, H:i:s') ?? '—' }}
                </div>
            </div>

            <div style="min-width:0;">
                <div style="display:flex; align-items:center; gap:8px; position:relative; z-index:1;">
                    <span style="display:block; width:16px; height:16px; flex:0 0 16px; border-radius:9999px; background:var(--primary-500,#f59e0b); box-shadow:0 0 0 4px color-mix(in srgb, var(--primary-500,#f59e0b) 15%, transparent);"></span>
                    <span style="font-weight:600; color:rgb(17 24 39);">Awaiting approval</span>
                </div>
                <div style="margin:8px 0 0 24px; font-size:14px; color:rgb(107 114 128);">
                    @if ($approvers->isNotEmpty())
                        <div style="margin-bottom:4px;">Eligible approvers</div>
                        <ul style="margin:0; padding-left:16px;">
                            @foreach ($approvers as $approver)
                                <li>{{ $approver->name }} <span style="opacity:.75;">({{ $approver->email }})</span></li>
                            @endforeach
                        </ul>
                    @else
                        No eligible approvers found.
                    @endif
                </div>
            </div>

            <div style="min-width:0;">
                <div style="display:flex; align-items:center; gap:8px; position:relative; z-index:1;">
                    <span style="display:block; width:16px; height:16px; flex:0 0 16px; border-radius:9999px; background:{{ match ($status) { \App\Enums\AccessRequestStatus::Approved, \App\Enums\AccessRequestStatus::Revoked => 'var(--primary-500,#f59e0b)', \App\Enums\AccessRequestStatus::Rejected => '#ef4444', default => '#d1d5db' } }}; box-shadow:0 0 0 4px rgba(107,114,128,.12);"></span>
                    <span style="font-weight:600; color:rgb(17 24 39);">{{ $decisionLabel }}</span>
                </div>
                <div style="margin:8px 0 0 24px; font-size:14px; color:rgb(107 114 128);">
                    @if ($record->decided_at)
                        <div>{{ $record->decided_at->format('d M Y, H:i:s') }}</div>
                        @if ($record->decided_by_id && $record->decided_by_type)
                            @php
                                $decisionMaker = $record->decided_by_type === \App\Models\Developer::class
                                    ? \App\Models\Developer::query()->find($record->decided_by_id)
                                    : \App\Models\User::query()->find($record->decided_by_id);
                            @endphp
                            @if ($decisionMaker)
                                <div style="margin-top:4px;">By: {{ $decisionMaker->name }}</div>
                            @endif
                        @endif
                        @if ($record->decision_reason)
                            <div style="margin-top:8px; color:rgb(55 65 81);">Reason: {{ $record->decision_reason }}</div>
                        @endif
                    @else
                        Not decided yet.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
