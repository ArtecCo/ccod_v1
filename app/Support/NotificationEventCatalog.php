<?php

namespace App\Support;

class NotificationEventCatalog
{
    /**
     * The complete configurable event catalogue.
     *
     * Each event owns its own context fields. The condition builder only exposes
     * fields from the selected event, so unrelated event data cannot be used.
     */
    private const EVENTS = [
        // Server (1)
        'server.started' => ['label' => 'Server started', 'category' => 'Server', 'fields' => ['server_boot_time', 'iis_status', 'iis_started_at', 'php_status', 'php_started_at', 'mysql_status', 'mysql_started_at', 'queue_status', 'queue_started_at']],

        // Users (9)
        'user.created' => ['label' => 'User created', 'category' => 'Users', 'fields' => ['user_id', 'user_name', 'user_email', 'user_status', 'role_name', 'occurred_at']],
        'user.activated' => ['label' => 'User activated', 'category' => 'Users', 'fields' => ['user_id', 'user_name', 'user_email', 'occurred_at']],
        'user.deactivated' => ['label' => 'User deactivated', 'category' => 'Users', 'fields' => ['user_id', 'user_name', 'user_email', 'occurred_at']],
        'user.deleted' => ['label' => 'User deleted', 'category' => 'Users', 'fields' => ['user_id', 'user_name', 'user_email', 'occurred_at']],
        'user.logged_in' => ['label' => 'User logged in', 'category' => 'Users', 'fields' => ['user_id', 'user_name', 'user_email', 'ip_address', 'occurred_at']],
        'user.group_added' => ['label' => 'User added to group', 'category' => 'Users', 'fields' => ['user_id', 'user_name', 'user_email', 'group_id', 'group_name', 'occurred_at']],
        'user.group_removed' => ['label' => 'User removed from group', 'category' => 'Users', 'fields' => ['user_id', 'user_name', 'user_email', 'group_id', 'group_name', 'occurred_at']],
        'user.invitation_sent' => ['label' => 'User invitation sent', 'category' => 'Users', 'fields' => ['user_id', 'user_name', 'user_email', 'invitation_expires_at', 'occurred_at']],

        // Subscriptions (5)
        'subscription.added' => ['label' => 'Subscription added', 'category' => 'Subscriptions', 'fields' => ['subscription_id', 'subscription_name', 'tenant_id', 'subscription_state', 'authorization_source', 'occurred_at']],
        'subscription.removed' => ['label' => 'Subscription removed', 'category' => 'Subscriptions', 'fields' => ['subscription_id', 'subscription_name', 'tenant_id', 'subscription_state', 'authorization_source', 'occurred_at']],
        'subscription.project_assigned' => ['label' => 'Subscription assigned to project', 'category' => 'Subscriptions', 'fields' => ['subscription_id', 'subscription_name', 'project_id', 'project_name', 'occurred_at']],
        'subscription.project_removed' => ['label' => 'Subscription removed from project', 'category' => 'Subscriptions', 'fields' => ['subscription_id', 'subscription_name', 'project_id', 'project_name', 'occurred_at']],
        'subscription.credentials_expired' => ['label' => 'Subscription credentials expired', 'category' => 'Subscriptions', 'fields' => ['subscription_id', 'subscription_name', 'credential_type', 'expires_at', 'occurred_at']],

        // Projects (4)
        'project.created' => ['label' => 'Project created', 'category' => 'Projects', 'fields' => ['project_id', 'project_name', 'description', 'occurred_at']],
        'project.deleted' => ['label' => 'Project deleted', 'category' => 'Projects', 'fields' => ['project_id', 'project_name', 'occurred_at']],
        'project.group_added' => ['label' => 'Project added to user group', 'category' => 'Projects', 'fields' => ['project_id', 'project_name', 'group_id', 'group_name', 'occurred_at']],
        'project.group_removed' => ['label' => 'Project removed from user group', 'category' => 'Projects', 'fields' => ['project_id', 'project_name', 'group_id', 'group_name', 'occurred_at']],

        // Business units (2)
        'business_unit.created' => ['label' => 'Business unit created', 'category' => 'Business Units', 'fields' => ['business_unit_id', 'business_unit_name', 'occurred_at']],
        'business_unit.deleted' => ['label' => 'Business unit deleted', 'category' => 'Business Units', 'fields' => ['business_unit_id', 'business_unit_name', 'occurred_at']],

        // User groups (3)
        'user_group.created' => ['label' => 'User group created', 'category' => 'User Groups', 'fields' => ['group_id', 'group_name', 'role_name', 'occurred_at']],
        'user_group.deleted' => ['label' => 'User group deleted', 'category' => 'User Groups', 'fields' => ['group_id', 'group_name', 'occurred_at']],
        'user_group.role_changed' => ['label' => 'User group role changed', 'category' => 'User Groups', 'fields' => ['group_id', 'group_name', 'old_role', 'new_role', 'occurred_at']],

        // Azure (12)
        'sync.started' => ['label' => 'Azure synchronization started', 'category' => 'Azure', 'fields' => ['run_id', 'sync_started_at']],
        'azure.sync.failed' => ['label' => 'Azure synchronization failed', 'category' => 'Azure', 'fields' => ['run_id', 'failed_at', 'error_summary', 'failed_modules', 'failed_module_errors', 'occurred_at']],
        'sync.completed' => ['label' => 'Azure synchronization completed', 'category' => 'Azure', 'fields' => [
            'run_id', 'overall_status', 'sync_started_at', 'completed_at', 'duration', 'duration_seconds',
            'subscriptions_status', 'resources_status', 'costs_status', 'security_status', 'alerts_status',
            'web_urls_status', 'failed_modules', 'successful_modules', 'error_summary', 'report_url',
        ]],
        'azure.resource.discovered' => ['label' => 'Azure resource discovered', 'category' => 'Azure', 'fields' => ['resource_id', 'resource_name', 'resource_type', 'resource_group', 'subscription_id', 'subscription_name', 'location', 'occurred_at']],
        'azure.resource.deleted' => ['label' => 'Azure resource deleted', 'category' => 'Azure', 'fields' => ['resource_id', 'resource_name', 'resource_type', 'resource_group', 'subscription_id', 'subscription_name', 'location', 'occurred_at']],
        'azure.alert.detected' => ['label' => 'Azure alert detected', 'category' => 'Azure', 'fields' => ['alert_id', 'alert_name', 'severity', 'status', 'resource_id', 'resource_name', 'subscription_id', 'subscription_name', 'occurred_at']],
        'azure.recommendation.detected' => ['label' => 'Defender recommendation detected', 'category' => 'Azure', 'fields' => ['recommendation_id', 'recommendation_name', 'category', 'severity', 'resource_id', 'resource_name', 'subscription_id', 'subscription_name', 'occurred_at']],
        'azure.recommendation.removed' => ['label' => 'Defender recommendation resolved or removed', 'category' => 'Azure', 'fields' => ['recommendation_id', 'recommendation_name', 'category', 'severity', 'resource_id', 'resource_name', 'subscription_id', 'subscription_name', 'occurred_at']],
        'azure.critical_alert.detected' => ['label' => 'Critical Azure alert detected', 'category' => 'Azure', 'fields' => ['alert_id', 'alert_name', 'severity', 'status', 'resource_id', 'resource_name', 'subscription_id', 'subscription_name', 'occurred_at']],
        'azure.security_score.decreased' => ['label' => 'Azure security score decreased', 'category' => 'Azure', 'fields' => ['subscription_id', 'subscription_name', 'previous_score', 'current_score', 'score_delta', 'occurred_at']],
        'azure.security_score.improved' => ['label' => 'Azure security score improved', 'category' => 'Azure', 'fields' => ['subscription_id', 'subscription_name', 'previous_score', 'current_score', 'score_delta', 'occurred_at']],
        'azure.budget.threshold_reached' => ['label' => 'Azure budget threshold reached', 'category' => 'Azure', 'fields' => ['budget_id', 'budget_name', 'subscription_id', 'subscription_name', 'actual_cost', 'budget_amount', 'threshold_percent', 'currency', 'occurred_at']],

        // Web URLs (7)
        'web_url.ssl.expiring' => ['label' => 'Website SSL certificate expiring', 'category' => 'Web URLs', 'fields' => ['web_url_id', 'url', 'domain', 'subscription_id', 'subscription_name', 'certificate_expires_at', 'days_until_expiry', 'ssl_reminder_threshold_days', 'severity', 'occurred_at']],
        'web_url.down' => ['label' => 'Website unavailable', 'category' => 'Web URLs', 'fields' => ['web_url_id', 'url', 'domain', 'http_status', 'response_time_ms', 'checked_at', 'occurred_at']],
        'web_url.deactivated' => ['label' => 'Website monitoring deactivated', 'category' => 'Web URLs', 'fields' => ['web_url_id', 'url', 'domain', 'subscription_id', 'subscription_name', 'occurred_at']],
        'web_url.activated' => ['label' => 'Website monitoring activated', 'category' => 'Web URLs', 'fields' => ['web_url_id', 'url', 'domain', 'subscription_id', 'subscription_name', 'occurred_at']],
        'web_url.ssl.expired' => ['label' => 'Website SSL certificate expired', 'category' => 'Web URLs', 'fields' => ['web_url_id', 'url', 'domain', 'certificate_expires_at', 'days_since_expiry', 'occurred_at']],
        'web_url.ssl.renewed' => ['label' => 'Website SSL certificate renewed', 'category' => 'Web URLs', 'fields' => ['web_url_id', 'url', 'domain', 'previous_expires_at', 'certificate_expires_at', 'occurred_at']],
        'web_url.recovered' => ['label' => 'Website recovered', 'category' => 'Web URLs', 'fields' => ['web_url_id', 'url', 'domain', 'http_status', 'response_time_ms', 'checked_at', 'occurred_at']],
    ];

    /**
     * Existing hardcoded in-app notifications remain independent of user rules.
     * They are intentionally not added to the configurable 42-event dropdown.
     */
    private const MANDATORY_EVENTS = [
        'access_request.submitted' => ['label' => 'Access request submitted', 'category' => 'Access Requests', 'mandatory_in_app' => true, 'mandatory_audience' => 'approvers', 'fields' => ['request_id', 'user_id', 'user_name', 'user_group_id', 'user_group_name', 'occurred_at']],
        'access_request.accepted' => ['label' => 'Access request accepted', 'category' => 'Access Requests', 'mandatory_in_app' => true, 'mandatory_audience' => 'requester', 'fields' => ['request_id', 'user_id', 'user_group_id', 'user_group_name', 'occurred_at']],
        'access_request.rejected' => ['label' => 'Access request rejected', 'category' => 'Access Requests', 'mandatory_in_app' => true, 'mandatory_audience' => 'requester', 'fields' => ['request_id', 'user_id', 'user_group_id', 'user_group_name', 'occurred_at']],
    ];

    private const FIELD_LABELS = [
        'server_boot_time' => 'Server boot time', 'iis_status' => 'IIS status', 'iis_started_at' => 'IIS started at',
        'php_status' => 'PHP status', 'php_started_at' => 'PHP started at', 'mysql_status' => 'MySQL status',
        'mysql_started_at' => 'MySQL started at', 'queue_status' => 'Queue worker status', 'queue_started_at' => 'Queue worker started at',
        'user_id' => 'User ID', 'user_name' => 'User name', 'user_email' => 'User email',
        'user_status' => 'User status', 'role_name' => 'Role', 'old_role' => 'Previous role',
        'new_role' => 'New role', 'ip_address' => 'IP address', 'invitation_expires_at' => 'Invitation expiry',
        'group_id' => 'Group ID', 'group_name' => 'Group name',
        'subscription_id' => 'Subscription ID', 'subscription_name' => 'Subscription name',
        'tenant_id' => 'Tenant ID', 'subscription_state' => 'Subscription state',
        'authorization_source' => 'Authorization source', 'project_id' => 'Project ID',
        'project_name' => 'Project name', 'credential_type' => 'Credential type', 'expires_at' => 'Expiry time',
        'description' => 'Description', 'business_unit_id' => 'Business unit ID',
        'business_unit_name' => 'Business unit name', 'run_id' => 'Sync run ID',
        'sync_started_at' => 'Sync start time', 'failed_at' => 'Failure time',
        'overall_status' => 'Overall status', 'completed_at' => 'Completion time',
        'duration' => 'Duration', 'duration_seconds' => 'Duration (seconds)',
        'subscriptions_status' => 'Subscriptions module status', 'resources_status' => 'Resources module status',
        'costs_status' => 'Costs module status', 'security_status' => 'Security module status',
        'alerts_status' => 'Alerts module status', 'web_urls_status' => 'Web URLs module status',
        'failed_modules' => 'Failed modules', 'successful_modules' => 'Successful modules',
        'error_summary' => 'Error summary', 'report_url' => 'Sync report URL',
        'resource_id' => 'Resource ID', 'resource_name' => 'Resource name', 'resource_type' => 'Resource type',
        'resource_group' => 'Resource group', 'location' => 'Azure location',
        'alert_id' => 'Alert ID', 'alert_name' => 'Alert name', 'severity' => 'Severity',
        'status' => 'Status', 'recommendation_id' => 'Recommendation ID',
        'recommendation_name' => 'Recommendation', 'category' => 'Category',
        'previous_score' => 'Previous security score', 'current_score' => 'Current security score',
        'score_delta' => 'Score change', 'budget_id' => 'Budget ID', 'budget_name' => 'Budget name',
        'actual_cost' => 'Actual cost', 'budget_amount' => 'Budget amount',
        'threshold_percent' => 'Threshold percentage', 'currency' => 'Currency',
        'web_url_id' => 'Web URL ID', 'url' => 'Website URL', 'domain' => 'Domain',
        'certificate_expires_at' => 'Certificate expiry', 'days_until_expiry' => 'Days until expiry', 'ssl_reminder_threshold_days' => 'Reminder threshold (days)',
        'http_status' => 'HTTP status', 'response_time_ms' => 'Response time (ms)',
        'checked_at' => 'Last checked at', 'days_since_expiry' => 'Days since expiry',
        'previous_expires_at' => 'Previous certificate expiry', 'occurred_at' => 'Occurred at',
        'request_id' => 'Request ID', 'user_group_id' => 'User group ID', 'user_group_name' => 'User group name',
    ];

    private const ENUM_VALUES = [
        'user_status' => ['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive'],
        'subscription_state' => ['Enabled' => 'Enabled', 'Warned' => 'Warned', 'PastDue' => 'Past due', 'Disabled' => 'Disabled', 'Deleted' => 'Deleted'],
        'authorization_source' => ['RoleBased' => 'Role based', 'Legacy' => 'Legacy'],
        'overall_status' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed'],
        'subscriptions_status' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed'],
        'resources_status' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed'],
        'costs_status' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed'],
        'security_status' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed'],
        'alerts_status' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed'],
        'web_urls_status' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed'],
        'severity' => ['CRITICAL' => 'Critical', 'HIGH' => 'High', 'MEDIUM' => 'Medium', 'LOW' => 'Low', 'INFO' => 'Informational', 'WARNING' => 'Warning', 'ATTENTION' => 'Attention'],
        'status' => ['ACTIVE' => 'Active', 'RESOLVED' => 'Resolved', 'DISMISSED' => 'Dismissed'],
    ];

    public static function all(): array
    {
        $events = self::EVENTS;

        foreach (self::MANDATORY_EVENTS as $key => $event) {
            $events[$key] = $event;
        }

        // Preserve the hardcoded in-app notifications for events that already
        // have mandatory delivery. Configurable webhook rules remain available.
        $mandatoryInApp = [
            'subscription.added' => 'super_admins',
            'subscription.removed' => 'super_admins',
            'user_group.role_changed' => 'group_members',
            'project.group_added' => 'group_members',
            'project.group_removed' => 'group_members',
            'subscription.project_assigned' => 'project_users',
            'subscription.project_removed' => 'project_users',
            'azure.sync.failed' => 'all_active_users',
        ];

        foreach ($events as $key => &$event) {
            $event['description'] ??= $event['label'];
            $event['scope'] ??= ['global'];
            $event['fields'] = self::buildFields($event['fields'] ?? []);
            $event['sample'] ??= self::buildSample($event['fields']);
            $event['mandatory_in_app'] = isset($mandatoryInApp[$key]) || ($event['mandatory_in_app'] ?? false);
            $event['mandatory_audience'] ??= $mandatoryInApp[$key] ?? null;
        }
        unset($event);

        return $events;
    }

    public static function configurable(): array
    {
        return self::EVENTS;
    }

    public static function options(): array
    {
        return collect(self::EVENTS)
            ->mapWithKeys(fn (array $event, string $key): array => [$key => $event['label']])
            ->all();
    }

    public static function groupedOptions(): array
    {
        $groups = [];
        foreach (self::EVENTS as $key => $event) {
            $groups[$event['category']][$key] = $event['label'];
        }
        ksort($groups);

        return $groups;
    }

    public static function event(string $key): ?array
    {
        $key = self::normalizeKey($key);

        return self::all()[$key] ?? null;
    }

    public static function normalizeKey(string $key): string
    {
        return match ($key) {
            'azure.sync.completed' => 'sync.completed',
            'azure.sync.started' => 'sync.started',
            'azure.sync_failed', 'azure_sync_failed' => 'azure.sync.failed',
            'azure_sync_succeeded' => 'sync.completed',
            'azure_subscription_connected' => 'subscription.added',
            'azure_subscription_disconnected' => 'subscription.removed',
            'user_group.member_added' => 'user.group_added',
            'project.subscription_assigned' => 'subscription.project_assigned',
            'project.subscription_removed' => 'subscription.project_removed',
            default => $key,
        };
    }

    public static function normalizeKeys(array|string|null $keys): array
    {
        $keys = is_array($keys) ? $keys : (is_string($keys) && $keys !== '' ? [$keys] : []);

        return array_values(array_unique(array_map(
            static fn ($key): string => self::normalizeKey((string) $key),
            $keys,
        )));
    }

    public static function fieldsForEvents(array|string|null $eventKeys): array
    {
        $fields = [];
        foreach (self::normalizeKeys($eventKeys) as $eventKey) {
            foreach (self::event($eventKey)['fields'] ?? [] as $key => $definition) {
                $fields[$key] = $definition;
            }
        }

        return $fields;
    }

    public static function fieldOptions(array|string|null $eventKeys): array
    {
        return collect(self::fieldsForEvents($eventKeys))
            ->mapWithKeys(fn (array $field, string $key): array => [$key => $field['label'] ?? $key])
            ->all();
    }

    public static function field(array|string|null $eventKeys, ?string $fieldKey): ?array
    {
        return $fieldKey ? (self::fieldsForEvents($eventKeys)[$fieldKey] ?? null) : null;
    }

    public static function operatorsForField(array|string|null $eventKeys, ?string $fieldKey): array
    {
        $field = self::field($eventKeys, $fieldKey);
        if (! $field) {
            return [];
        }

        $type = $field['type'];
        $operators = ['exists' => 'Exists', 'not_exists' => 'Does not exist', 'equals' => 'Equals', 'not_equals' => 'Does not equal'];

        if (in_array($type, ['string', 'enum', 'array', 'url'], true)) {
            $operators += ['contains' => 'Contains', 'not_contains' => 'Does not contain', 'in' => 'Is one of'];
        }

        if (in_array($type, ['number', 'datetime'], true)) {
            $operators += ['greater_than' => 'Greater than', 'greater_or_equal' => 'Greater than or equal', 'less_than' => 'Less than', 'less_or_equal' => 'Less than or equal'];
        }

        return $operators;
    }

    public static function valuesForField(array|string|null $eventKeys, ?string $fieldKey): array
    {
        return self::field($eventKeys, $fieldKey)['values'] ?? [];
    }

    public static function placeholderOptions(array|string|null $eventKeys): array
    {
        return collect(self::fieldsForEvents($eventKeys))
            ->mapWithKeys(fn (array $field, string $key): array => ['{' . $key . '}' => ($field['label'] ?? $key) . ' — {' . $key . '}'])
            ->all();
    }

    public static function inAppDefaults(string $eventKey): array
    {
        return match (self::normalizeKey($eventKey)) {
            'sync.completed' => [
                'title' => 'Azure sync completed',
                'message' => 'Overall status: {overall_status}',
                'type' => 'general',
                'severity' => 'info',
                'action_label' => 'View Sync Report',
                'action_url' => '{report_url}',
            ],
            'azure.sync.failed' => [
                'title' => 'Azure sync failed',
                'message' => '{error_summary}',
                'type' => 'alert',
                'severity' => 'critical',
            ],
            default => [],
        };
    }

    public static function sampleContext(string $eventKey): array
    {
        $eventKey = self::normalizeKey($eventKey);
        $event = self::event($eventKey);
        $context = $event['sample'] ?? [];
        $context['event_key'] = $eventKey;
        $context['event_label'] = $event['label'] ?? $eventKey;
        $context['is_test'] = true;
        $context['test_timestamp'] = now()->toIso8601String();

        if ($eventKey === 'sync.completed') {
            $now = now();
            $context['sync_started_at'] = $now->copy()->subMinutes(3)->subSeconds(12)->format('Y-m-d H:i:s T');
            $context['completed_at'] = $now->format('Y-m-d H:i:s T');
            $context['run_id'] = 'TEST-'.$now->format('Ymd-His');
            $context['report_url'] = rtrim((string) config('app.url'), '/').'/sync-report/latest';
        }

        return $context;
    }

    private static function buildFields(array $keys): array
    {
        $fields = [];
        foreach ($keys as $key) {
            $type = match (true) {
                in_array($key, ['duration_seconds', 'previous_score', 'current_score', 'score_delta', 'actual_cost', 'budget_amount', 'threshold_percent', 'days_until_expiry', 'ssl_reminder_threshold_days', 'days_since_expiry', 'http_status', 'response_time_ms'], true) => 'number',
                in_array($key, ['occurred_at', 'sync_started_at', 'failed_at', 'completed_at', 'expires_at', 'invitation_expires_at', 'certificate_expires_at', 'previous_expires_at', 'checked_at', 'server_boot_time', 'iis_started_at', 'php_started_at', 'mysql_started_at', 'queue_started_at'], true) => 'datetime',
                in_array($key, ['failed_modules', 'successful_modules'], true) => 'array',
                $key === 'report_url' || $key === 'url' => 'url',
                array_key_exists($key, self::ENUM_VALUES) => 'enum',
                default => 'string',
            };
            $fields[$key] = [
                'label' => self::FIELD_LABELS[$key] ?? str($key)->replace('_', ' ')->title()->toString(),
                'type' => $type,
            ];
            if ($type === 'enum') {
                $fields[$key]['values'] = self::ENUM_VALUES[$key];
            }
        }

        return $fields;
    }

    private static function buildSample(array $fields): array
    {
        $sample = [];
        foreach ($fields as $key => $field) {
            $sample[$key] = match ($field['type']) {
                'number' => 1,
                'datetime' => now()->toIso8601String(),
                'array' => [],
                'enum' => array_key_first($field['values'] ?? []) ?? 'ACTIVE',
                'url' => 'https://example.com',
                default => 'Sample '.($field['label'] ?? $key),
            };
        }

        if (isset($sample['overall_status'])) {
            $sample['overall_status'] = 'FAILED';
            foreach (['subscriptions_status', 'resources_status', 'costs_status', 'security_status', 'alerts_status', 'web_urls_status'] as $field) {
                if (isset($sample[$field])) {
                    $sample[$field] = 'SUCCESS';
                }
            }
            $sample['web_urls_status'] = 'FAILED';
            $sample['failed_modules'] = ['Web URLs'];
            $sample['successful_modules'] = ['Subscriptions', 'Resources', 'Costs', 'Security', 'Alerts'];
            $sample['error_summary'] = 'Web URLs synchronization failed.';
            $sample['duration'] = '3m 12.147s';
            $sample['duration_seconds'] = 192.147;
        }

        return $sample;
    }
}
