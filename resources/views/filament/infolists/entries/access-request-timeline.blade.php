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
    $isApproved = in_array($record->status, [\App\Enums\AccessRequestStatus::Approved, \App\Enums\AccessRequestStatus::Revoked], true);
    $isRejected = $record->status === \App\Enums\AccessRequestStatus::Rejected;
@endphp

<div class="access-request-stepper">
    <style>
        .access-request-stepper {
            --step-line: #d1d5db;
            --step-muted: #6b7280;
            --step-text: #111827;
            --step-surface: #ffffff;
            --step-border: #e5e7eb;
            --step-accent: var(--primary-600);
        }

        .dark .access-request-stepper {
            --step-line: #4b5563;
            --step-muted: #9ca3af;
            --step-text: #f3f4f6;
            --step-surface: #111827;
            --step-border: #374151;
        }

        .access-request-stepper__track {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            align-items: start;
            position: relative;
            min-height: 4.25rem;
        }

        .access-request-stepper__connector {
            position: absolute;
            z-index: 0;
            top: 0.8125rem;
            left: 16.666667%;
            right: 16.666667%;
            height: 2px;
            background: var(--step-line);
        }

        .access-request-stepper__connector--complete {
            background: var(--step-accent);
        }

        .access-request-stepper__stage {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 0;
            padding: 0 0.75rem;
        }

        .access-request-stepper__node {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1.625rem;
            height: 1.625rem;
            border: 2px solid var(--step-line);
            border-radius: 9999px;
            background: var(--step-surface);
            color: var(--step-muted);
            box-shadow: 0 0 0 4px var(--step-surface);
        }

        .access-request-stepper__node--complete,
        .access-request-stepper__node--current {
            border-color: var(--step-accent);
            background: var(--step-accent);
            color: #ffffff;
        }

        .access-request-stepper__node--rejected {
            border-color: #ef4444;
            background: #ef4444;
            color: #ffffff;
        }

        .access-request-stepper__node svg {
            width: 0.875rem;
            height: 0.875rem;
        }

        .access-request-stepper__heading {
            margin-top: 0.625rem;
            text-align: center;
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 600;
            color: var(--step-text);
        }

        .access-request-stepper__status {
            margin-top: 0.125rem;
            text-align: center;
            font-size: 0.75rem;
            line-height: 1rem;
            color: var(--step-muted);
        }

        .access-request-stepper__cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .access-request-stepper__card {
            min-width: 0;
            padding: 0.875rem 1rem;
            border: 1px solid var(--step-border);
            border-radius: 0.5rem;
            background: var(--step-surface);
        }

        .access-request-stepper__card-label {
            font-size: 0.6875rem;
            line-height: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--step-muted);
        }

        .access-request-stepper__card-value {
            margin-top: 0.25rem;
            font-size: 0.8125rem;
            line-height: 1.25rem;
            color: var(--step-text);
        }

        .access-request-stepper__card-value--muted {
            color: var(--step-muted);
        }

        .access-request-stepper__approvers {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin-top: 0.375rem;
        }

        .access-request-stepper__approver {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .access-request-stepper__track,
            .access-request-stepper__cards {
                grid-template-columns: 1fr;
            }

            .access-request-stepper__connector {
                top: 0.8125rem;
                bottom: 0.8125rem;
                left: 0.8125rem;
                right: auto;
                width: 2px;
                height: auto;
            }

            .access-request-stepper__stage {
                display: grid;
                grid-template-columns: 1.625rem minmax(0, 1fr);
                column-gap: 0.75rem;
                align-items: center;
                padding: 0;
                margin-bottom: 1rem;
            }

            .access-request-stepper__stage:last-child {
                margin-bottom: 0;
            }

            .access-request-stepper__heading,
            .access-request-stepper__status {
                margin-top: 0;
                text-align: left;
            }

            .access-request-stepper__heading {
                grid-column: 2;
            }

            .access-request-stepper__status {
                grid-column: 2;
            }

            .access-request-stepper__node {
                grid-row: 1 / span 2;
            }
        }
    </style>

    <div class="access-request-stepper__track">
        <div class="access-request-stepper__connector {{ $isDecided ? 'access-request-stepper__connector--complete' : '' }}"></div>

        <div class="access-request-stepper__stage">
            <div class="access-request-stepper__node access-request-stepper__node--complete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>
            </div>
            <div class="access-request-stepper__heading">Submitted</div>
            <div class="access-request-stepper__status">{{ $record->created_at?->format('d M Y, H:i:s') ?? '—' }}</div>
        </div>

        <div class="access-request-stepper__stage">
            <div class="access-request-stepper__node {{ $isDecided ? 'access-request-stepper__node--complete' : 'access-request-stepper__node--current' }}">
                @if ($isDecided)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/></svg>
                @endif
            </div>
            <div class="access-request-stepper__heading">Awaiting approval</div>
            <div class="access-request-stepper__status">{{ $approvers->count() }} eligible {{ $approvers->count() === 1 ? 'approver' : 'approvers' }}</div>
        </div>

        <div class="access-request-stepper__stage">
            <div class="access-request-stepper__node {{ $isApproved ? 'access-request-stepper__node--complete' : ($isRejected ? 'access-request-stepper__node--rejected' : '') }}">
                @if ($isApproved)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>
                @elseif ($isRejected)
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6 6 18"/></svg>
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/></svg>
                @endif
            </div>
            <div class="access-request-stepper__heading">Decision</div>
            <div class="access-request-stepper__status">{{ $decisionStatus }}</div>
        </div>
    </div>

    <div class="access-request-stepper__cards">
        <div class="access-request-stepper__card">
            <div class="access-request-stepper__card-label">Submission</div>
            <div class="access-request-stepper__card-value">
                {{ $record->user?->name ?? 'The requester' }} submitted this request.
            </div>
        </div>

        <div class="access-request-stepper__card">
            <div class="access-request-stepper__card-label">Eligible approvers</div>
            @if ($approvers->isNotEmpty())
                <div class="access-request-stepper__approvers access-request-stepper__card-value">
                    @foreach ($approvers as $approver)
                        <div class="access-request-stepper__approver">{{ $approver->name }} · {{ $approver->email }}</div>
                    @endforeach
                </div>
            @else
                <div class="access-request-stepper__card-value access-request-stepper__card-value--muted">No eligible approvers found.</div>
            @endif
        </div>

        <div class="access-request-stepper__card">
            <div class="access-request-stepper__card-label">Decision details</div>
            @if ($isDecided)
                <div class="access-request-stepper__card-value">
                    {{ $record->decided_at?->format('d M Y, H:i:s') }}
                    @if ($decisionModel?->name)
                        · by {{ $decisionModel->name }}
                    @endif
                    @if ($record->decision_reason)
                        <div>Decision reason: {{ $record->decision_reason }}</div>
                    @endif
                </div>
            @else
                <div class="access-request-stepper__card-value access-request-stepper__card-value--muted">Awaiting a decision from an eligible approver.</div>
            @endif
        </div>
    </div>
</div>
