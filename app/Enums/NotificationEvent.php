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

    // Current mandatory-event catalogue. These keys coexist with the legacy
    // enum values above so existing integrations remain backward compatible.
    case UserGroupMemberAdded = 'user_group.member_added';
    case UserGroupRoleChanged = 'user_group.role_changed';
    case AccessRequestSubmitted = 'access_request.submitted';
    case AccessRequestAccepted = 'access_request.accepted';
    case AccessRequestRejected = 'access_request.rejected';
    case ProjectAddedToGroup = 'project.group_added';
    case ProjectRemovedFromGroup = 'project.group_removed';
    case ProjectSubscriptionAssigned = 'project.subscription_assigned';
    case ProjectSubscriptionRemoved = 'project.subscription_removed';
    case GlobalSubscriptionAdded = 'azure.subscription_global_added';
    case GlobalSubscriptionRemoved = 'azure.subscription_global_removed';
    case SynchronizationFailed = 'azure.sync_failed';

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
            self::UserGroupMemberAdded => 'User added to user group',
            self::UserGroupRoleChanged => 'User group role changed',
            self::AccessRequestSubmitted => 'Access request submitted',
            self::AccessRequestAccepted => 'Access request accepted',
            self::AccessRequestRejected => 'Access request rejected',
            self::ProjectAddedToGroup => 'Project added to user group',
            self::ProjectRemovedFromGroup => 'Project removed from user group',
            self::ProjectSubscriptionAssigned => 'Subscription assigned to project',
            self::ProjectSubscriptionRemoved => 'Subscription removed from project',
            self::GlobalSubscriptionAdded => 'Global Azure subscription added',
            self::GlobalSubscriptionRemoved => 'Global Azure subscription removed',
            self::SynchronizationFailed => 'Azure synchronization failed',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $event): array => [$event->value => $event->label()])
            ->all();
    }
}
