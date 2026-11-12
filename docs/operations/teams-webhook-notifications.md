# Microsoft Teams webhook notifications

## Configuration model

The notification module separates four concepts:

- **Events** describe something that happened in CCOD. Producers dispatch an event key and context payload.
- **Categories** organise templates; they do not create triggers.
- **Templates** define reusable titles and messages and are associated with an event schema.
- **Destinations** hold the encrypted Teams URL, destination type, format, event subscriptions and optional conditions.

A destination can subscribe to multiple events. Multiple destinations can subscribe to the same event with different templates and conditions.

## Configure destinations

In Developer Panel → Notifications:

1. Create or select a category under Teams Message Templates.
2. Create a template and select its event schema.
3. Use **Insert event field** to append a valid placeholder to the message.
4. Create a Teams Webhook destination and select one or more trigger events.
5. Select a template category and template.
6. Optionally add conditions. Available fields, operators and enum values come from the selected event schemas.
7. Choose whether all or any conditions must match, then enable the destination.

Webhook URLs are encrypted using Laravel's application key. Never place webhook URLs in source control or logs. Use a Teams Workflows incoming webhook URL configured for the intended channel or chat. Do not use the retired Office 365 Connector webhook type. See [Microsoft's incoming webhook guidance](https://learn.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook).

Adaptive Cards are the default and are sent to both Teams channel and chat destinations. Legacy MessageCard remains available only for channel destinations.

## Central event catalogue

`App\Support\NotificationEventCatalog` is the source of truth for event keys, labels, categories, descriptions, scopes, payload field names, types, allowed values, condition operators and sample payloads. The event selector groups choices by category.

When a destination subscribes to multiple events, the condition builder displays the union of their fields. At dispatch time, a condition field must belong to the event currently being evaluated.

### Registered first-party events

| Event key | Purpose | Important fields |
|---|---|---|
| `server.started` | Windows server startup snapshot | `server_boot_time`, `iis_status`, `iis_started_at`, `php_status`, `php_started_at`, `mysql_status`, `mysql_started_at`, `queue_status`, `queue_started_at` |
| `sync.started` | Unified Azure sync begins | `run_id`, `sync_started_at` |
| `sync.completed` | Unified Azure sync finishes | `overall_status`, `duration_seconds`, `failed_modules`, `successful_modules`, `error_summary`, module status fields, timestamps and run ID |

The former `azure.sync.completed` key is normalized to `sync.completed`; existing webhook subscriptions are migrated to the canonical key. Existing application event keys from `App\Enums\NotificationEvent` are also listed. Their event-specific fields should be added to the catalogue when their producers expose a documented payload.

### Adding an event or field

1. Add the event metadata, fields, types, valid values and sample payload to `NotificationEventCatalog`.
2. Call `TeamsWebhookNotifier::dispatch($eventKey, $context)` from the real workflow where the event occurs.
3. Keep the registered fields aligned with the dispatched context. Do not add an event to the catalogue before its producer exists.

After registration, the event and its fields become available in the event selector, condition builder and template placeholder selector.

## Conditions

Available operators depend on field type:

- All fields: Equals, Does not equal, Exists, Does not exist.
- String, enum and array: Contains, Does not contain, Is one of.
- Number and date/time: Greater than, Greater than or equal, Less than, Less than or equal.

Example: subscribe to `sync.completed` and configure `overall_status` equals `FAILED` to receive only failed sync notifications. Use `duration_seconds` greater than `900` to match syncs taking more than 15 minutes.

## Windows startup task

Run `scripts\scheduler\register-server-start-task.cmd` from the application root in an Administrator Command Prompt. It registers a SYSTEM startup task with a two-minute delay using `C:\PHP\php.exe` and `scripts\scheduler\notify-server-start.php`.

The task needs read access to application files and access to the local database. PHP status is based on an active `php-cgi.exe` FastCGI worker, so it can report NOT DETECTED if IIS has not handled a PHP request after boot. IIS service start time is derived from its hosting process and can be approximate when Windows shares the service host process.

The queue worker is detected from the running `php.exe artisan queue:work` process and its process creation time. The existing `scripts\start-queue-worker.bat` task remains the queue worker launcher.

## Sync notifications

The unified `scripts\scheduler\sync-all.php` runner emits `sync.started` before executing the six modules and `sync.completed` after writing the report. The completion payload includes overall status, per-module status, duration in seconds, successful and failed module names, an error summary and the run ID. Standalone module scripts do not send duplicate aggregate notifications.

## Template placeholders

Use **Insert event field** to choose placeholders supported by the template's selected event. Common fields include `{event_key}`, `{event_label}`, `{is_test}` and `{test_timestamp}`. Server and sync fields are listed in the catalogue and do not need to be memorized or typed manually.

A placeholder absent from the dispatched event context renders as `Not available`.
