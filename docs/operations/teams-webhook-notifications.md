# Microsoft Teams webhook notifications

## Configuration model

The notification module separates four concepts:

- **Events** describe something that happened in CCOD. Producers dispatch an event key and a context payload.
- **Categories** organise message templates in the portal. A category does not itself create a trigger.
- **Templates** define reusable titles and messages. A template is associated with an event schema so its available placeholders can be selected.
- **Destinations** hold the encrypted delivery URL, Teams destination type, format, event subscriptions and optional conditions.

A destination can subscribe to multiple events. Multiple destinations can subscribe to the same event, each with a different template, format and set of conditions.

## Configure destinations

In Developer Panel → Notifications:

1. Create or select a category under Teams Message Templates.
2. Create one or more reusable templates in that category.
3. Select the event schema for each template.
4. Use **Insert event field** in the template editor to append a valid placeholder to the message.
5. Create a Teams Webhook destination.
6. Select one or more trigger events.
7. Select a template category and template.
8. Optionally add conditions. Event fields, valid operators and enum values are loaded from the selected event schemas.
9. Choose whether all or any conditions must match, then enable the destination.

Webhook URLs are encrypted using Laravel's application key. Never place webhook URLs in source control or logs. Use a Teams Workflows incoming webhook URL configured for the intended channel or chat. Do not use the retired Office 365 Connector webhook type: Microsoft retired connector-based incoming webhooks in May 2026. See [Microsoft's current incoming webhook guidance](https://learn.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook).

Channel destinations support Adaptive Cards or legacy MessageCard payloads. Chat destinations receive a plain-text body with Teams-supported Markdown; arbitrary font sizes and text colors are not supported. The receiving Teams Workflow must be configured to accept the JSON body and post its text to the intended chat.

## Central event catalogue

`App\Support\NotificationEventCatalog` + ` +  is the single source of truth for:

- Event keys, labels, categories, descriptions and allowed scopes.
- Event payload field names, display labels and data types.
- Enum/boolean values and supported condition operators.
- Sample payloads used by the Send test action.
- Selectable template placeholders.

The webhook event selector groups events by category. Selecting an event controls which fields appear in the condition builder. Numeric and date/time fields expose comparison operators; enum and boolean fields expose valid selectable values. If a destination subscribes to multiple events, the builder shows the union of their fields, and the evaluator rejects a condition field that is not part of the event currently being dispatched.

### Registered first-party events

| Event key | Purpose | Important fields |
|---|---|---|
| `server.started` | Windows server startup snapshot | `server_boot_time`, `iis_status`, `iis_started_at`, `php_status`, `php_started_at`, `mysql_status`, `mysql_started_at`, `queue_status`, `queue_started_at` |
| `sync.started` | Unified Azure sync begins | `run_id`, `sync_started_at` |
| `sync.completed` | Unified Azure sync finishes | `overall_status`, `duration_seconds`, `failed_modules`, `successful_modules`, `error_summary`, module status fields, timestamps and run ID` |

The old `azure.sync.completed` key is normalized to `sync.completed` for compatibility. Existing webhook subscriptions are migrated to the canonical key.

Existing application event keys from `App\Enums\NotificationEvent` are also included in the selector. Their event-specific fields should be added to this catalogue when their producers expose a documented payload.

### Adding an event or field

1. Register the stable key, label, description, scope, fields, types, valid values and sample payload in `NotificationEventCatalog::EVENTS`.
2. At the point where the event occurs, call `TeamsWebhookNotifier::dispatch($eventKey, $context)` once.
3. Ensure the context keys and value types match the registered schema.
4. Add a representative sample payload so Send test can preview the event.

After registration, the event and its fields automatically become available in the event selector, condition builder and template placeholder selector. No destination-specific script changes are needed.

## Conditions

Conditions filter the event context before delivery. The available operators depend on the selected field type:

- All fields: Equals, Does not equal, Exists, Does not exist.
- String, enum and array: Contains, Does not contain, Is one of.
- Number and date/time: Greater than, Greater than or equal, Less than, Less than or equal.

For example, subscribe to `sync.completed` and configure `overall_status` equals `FAILED` to receive only failed sync notifications. Or use `duration_seconds` greater than `900` to receive syncs that take more than 15 minutes.

## Windows startup task

After pulling the repository, open Command Prompt as Administrator and run `scripts\scheduler\register-server-start-task.cmd` from the application root. It registers a Windows startup task running as SYSTEM, with a two-minute delay, using `C:\PHP\php.exe` and `scripts\scheduler\notify-server-start.php`.

The task needs read access to the application files and access to the local database. The delay allows IIS, MySQL, and the queue task to initialize before the status snapshot is sent. The script reports the state it observes; a component that is still starting may be shown as not running. PHP status is based on an active `php-cgi.exe` FastCGI worker, so it can report NOT DETECTED if IIS has not yet handled a PHP request after boot. IIS service start time is derived from its hosting process and can be approximate when Windows shares the service host process.

## Queue worker

The queue worker is detected from the running `php.exe artisan queue:work` process and its process creation time. The existing `scripts\start-queue-worker.bat` task remains the queue worker launcher.

## Sync notifications

The unified `scripts\scheduler\sync-all.php` runner emits `sync.started` before executing the six modules and `sync.completed` after the report has been written. The completion payload includes overall status, per-module status, duration in seconds, successful/failed module names and an error summary. Standalone module scripts do not send duplicate aggregate notifications.

## Template placeholders

Use the **Insert event field** selector to insert placeholders supported by the template's selected event. Common fields include `{event_key}`, `{event_label}` and `{is_test}`. The server and sync event fields are listed in the catalogue and are therefore selectable rather than requiring users to memorize their names.

If a placeholder is not present in the dispatched event context, it renders as `Not available`.
