@php
    $authorization = app(\App\Services\AccessAuthorizationService::class);
    $approvers = $authorization->approvers($record);
    $decisionStatus = match ($record->status) {
        \App\Enums\AccessRequestStatus::Approved, \App\Enums\AccessRequestStatus::Revoked => 'Approved',
        \App\Enums\AccessRequestStatus::Rejected => 'Rejected',
        default => 'Awaiting decision',
    };
    $decisionModel = $record->decided_by_id && $record->decided_by_type
        ? ($record->decided_by_type === \App\Models\Developer::class
            ? \App\Models\Developer::query()->find($record->decided_by_id)
            : \App\Models\User::query()->find($record->decided_by_id))
        : null;
    $isDecided = $record->decided_at !== null;
@endphp

<div class="access-request-timeline">
    <style>
        .access-request-timeline {
            --timeline-line: rgb(209 213 219);
            --timeline-complete: rgb(17 24 39);
            --timeline-muted: rgb(107 114 128);
            --timeline-accent: rgb(var(--primary-600));
        }

        .dark .access-request-timeline {
            --timeline-line: rgb(75 85 99);
            --timeline-complete: rgb(229 231 235);
            --timeline-muted: rgb(156 163 175);
        }

        .access-request-timeline__item {
            position: relative;
            display: grid;
            grid-template-columns: 1.5rem minmax(0, 1fr);
            column-gap: 0.75rem;
            padding-bottom: 1.25rem;
        }

        .access-request-timeline__item:last-child {
            padding-bottom: 0;
        }

        .access-request-timeline__rail {
            position: absolute;
            top: 0.625rem;
            bottom: -0.625rem;
            left: 0.375rem;
            width: 1px;
            background: var(--timeline-line);
        }

        .access-request-timeline__item--complete .access-request-timeline__rail {
            background: var(--timeline-complete);
        }

        .access-request-timeline__dot {
            position: relative;
            z-index: 1;
            width: 0.75rem;
            height: 0.75rem;
            margin-top: 0.25rem;
            border: 2px solid var(--timeline-line);
            border-radius: 9999px;
            background: var(--timeline-line);
            box-sizing: border-box;
        }

        .access-request-timeline__item--complete .access-request-timeline__dot,
        .access-request-timeline__item--current .access-request-timeline__dot {
            border-color: var(--timeline-accent);
            background: var(--timeline-accent);
        }

        .access-request-timeline__title {
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 600;
            color: rgb(17 24 39);
        }

        .dark .access-request-timeline__title {
            color: rgb(243 244 246);
        }

        .access-request-timeline__meta {
            margin-top: 0.125rem;
            font-size: 0.75rem;
            line-height: 1rem;
            color: var(--timeline-muted);
        }

        .access-request-timeline__detail {
            margin-top: 0.375rem;
            font-size: 0.8125rem;
            line-height: 1.25rem;
            color: rgb(75 85 99);
        }

        .dark .access-request-timeline__detail {
            color: rgb(209 213 219);
        }
    </style>

    <div class="access-request-timeline__item access-request-timeline__item--complete">
        <div class="access-request-timeline__rail"></div>
        <div class="access-request-timeline__dot"></div>
        <div>
            <div class="access-request-timeline__title">Submitted</div>
            <div class="access-request-timeline__meta">
                {{ $record->created_at?->format('d M Y, H:i:s') ?? '—' }}
            </div>
            <div class="access-request-timeline__detail">
                {{ $record->user?->name ?? 'The requester' }} submitted the access request.
            </div>
        </div>
    </div>

    <div class="access-request-timeline__item {{ $isDecided ? 'access-request-timeline__item--complete' : 'access-request-timeline__item--current' }}">
        <div class="access-request-timeline__rail"></div>
        <div class="access-request-timeline__dot"></div>
        <div>
            <div class="access-request-timeline__title">Awaiting approval</div>
            <div class="access-request-timeline__meta">
                {{ $approvers->count() }} eligible {{ $approvers->count() === 1 ? 'approver' : 'approvers' }}
            </div>
            <div class="access-request-timeline__detail">
                @if ($approvers->isNotEmpty())
                    {{ $approvers->map(fn ($approver) => $approver->name . ' (' . $approver->email . ')')->implode(', ') }}
                @else
                    No eligible approvers found.
                @endif
            </div>
        </div>
    </div>

    <div class="access-request-timeline__item {{ $isDecided ? 'access-request-timeline__item--complete' : '' }}">
        <div class="access-request-timeline__dot"></div>
        <div>
            <div class="access-request-timeline__title">Decision</div>
            <div class="access-request-timeline__meta">{{ $decisionStatus }}</div>
            <div class="access-request-timeline__detail">
                {{ $record->decision_reason ?: 'No decision reason recorded.' }}
                @if ($isDecided)
                    <br>
                    {{ $record->decided_at?->format('d M Y, H:i:s') }}
                    @if ($decisionModel?->name)
                        · by {{ $decisionModel->name }}
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
