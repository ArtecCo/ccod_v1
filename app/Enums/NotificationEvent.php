<?php

namespace App\Enums;

enum NotificationEvent: string
{
    case AzureSubscriptionConnected = 'azure_subscription_connected';
    case AzureSubscriptionDisconnected = 'azure_subscription_disconnected';
    case AzureSyncSucceeded = 'azure_sync_succeeded';
    case AzureSyncFailed = 'azure_sync_failed';
    case AzureCriticalAlertDetected = 'azure_critical_alert_detected';
    case ServiceNowIncidentCreated = 'servicenow_incident_created';
    case ServiceNowIncidentResolved = 'servicenow_incident_resolved';
    case UserAddedToTeam = 'user_added_to_team';
    case UserRemovedFromTeam = 'user_removed_from_team';
    case SubscriptionAssignedToTeam = 'subscription_assigned_to_team';
    case SubscriptionRemovedFromTeam = 'subscription_removed_from_team';
    case MaintenanceScheduled = 'maintenance_scheduled';
    case MaintenanceCompleted = 'maintenance_completed';

    public function label(): string
    {
        return match ($this) {
            self::AzureSubscriptionConnected => 'Azure subscription connected',
            self::AzureSubscriptionDisconnected => 'Azure subscription disconnected',
            self::AzureSyncSucceeded => 'Azure synchronization succeeded',
            self::AzureSyncFailed => 'Azure synchronization failed',
            self::AzureCriticalAlertDetected => 'Critical Azure alert detected',
            self::ServiceNowIncidentCreated => 'ServiceNow incident created',
            self::ServiceNowIncidentResolved => 'ServiceNow incident resolved',
            self::UserAddedToTeam => 'User added to team',
            self::UserRemovedFromTeam => 'User removed from team',
            self::SubscriptionAssignedToTeam => 'Subscription assigned to team',
            self::SubscriptionRemovedFromTeam => 'Subscription removed from team',
            self::MaintenanceScheduled => 'Maintenance scheduled',
            self::MaintenanceCompleted => 'Maintenance completed',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $event): array => [$event->value => $event->label()])
            ->all();
    }
}
