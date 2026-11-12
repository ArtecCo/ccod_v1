# Microsoft Teams webhook notifications

## Configuration model

The notification module separates four concepts:

- **Events** describe something that happened in CCOD. Producers dispatch an event key and a context payload.
- **Categories** organise message templates in the portal. A category does not itself create a trigger.
- **Templates** define reusable titles and messages. A webhook destination links to one template.
- **Destinations** hold the encrypted delivery URL, Teams destination type, format, event subscriptions and optional conditions.

A destination can subscribe to multiple events. Multiple destinations can subscribe to the same event, each with a different template, format and set of conditions.

## Configure destinations

In Developer Panel → Notifications:

1. Create or select a category under Teams Message Templates.
2. Create one or more reusable templates in that category.
3. Create a Teams Webhook destination.
4. Select one or more trigger events.
5. Select a template category and template.
6. Optionally add conditions and choose whether all or any conditions must match.
7. Enable the destination.

Webhook URLs are encrypted using Laravel's application key. Never place webhook URLs in source control or logs. Use a Teams Workflows incoming webhook URL configured for the intended channel or chat. Do not use the retired Office 365 Connector webhook type: Microsoft retired connector-based incoming webhooks in May 2026. See [Microsoft's current incoming webhook guidance](https://learn.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook).

Channel destinations support Adaptive Cards or legacy MessageCard payloads. Chat destinations receive a plain-text body with Teams-supported Markdown; arbitrary font sizes and text colors are not supported. The receiving Teams Workflow must be configured to accept the JSON body and post its text to the intended chat.

## Event catalogue and extension

The event selector is populated by \`App\Support\TeamsWebhookEvents\`.

Currently registered event keys include:

- \`server.started\` — emitted by the Windows startup notification script.
- \`azure.sync.completed\` — emitted by the consolidated \`sync-all.php\` runner.
- Existing application event keys from \`App\Enums\NotificationEvent\`.

The existing \`NotificationRuleService::trigger()\` now dispatches the corresponding event to subscribed Teams destinations independently of whether an in-app notification rule exists or is enabled.

To expose a new event:

1. Add its stable event key and label to \`TeamsWebhookEvents::options()\`.
2. At the point where the event occurs, call \`TeamsWebhookNotifier::dispatch($eventKey, $context)\` once.
3. Pass a documented, non-sensitive context array.
4. Add a sample payload to \`TeamsWebhookEvents::sampleContext()\` so the portal's Send test action can preview realistic values.

Once registered, the event is selectable for any destination. No destination-specific script changes are needed.

## Conditions

Conditions filter the event context before delivery. Supported operators:

- Equals / does not equal
- Contains / does not contain
- Greater than / greater than or equal
- Less than / less than or equal
- Is one of (comma-separated values)
- Exists / does not exist

Choose whether all conditions or any condition must match. For example, subscribe to \`azure.sync.completed\` and add \`overall_status\` equals \`FAILED\` to receive only failed sync notifications.

## Windows startup task

After pulling the repository, open Command Prompt as Administrator and run \`scripts\\scheduler\\register-server-start-task.cmd\` from the application root. It registers a Windows startup task running as SYSTEM, with a two-minute delay, using \`C:\\PHP\\php.exe\` and \`scripts\\scheduler\\notify-server-start.php\`.

The task needs read access to the application files and access to the local database. The delay allows IIS, MySQL, and the queue task to initialize before the status snapshot is sent. The script reports the state it observes; a component that is still starting may be shown as not running. PHP status is based on an active \`php-cgi.exe\` FastCGI worker, so it can report NOT DETECTED if IIS has not yet handled a PHP request after boot. IIS service start time is derived from its hosting process and can be approximate when Windows shares the service host process.

## Queue worker

The queue worker is detected from the running \`php.exe artisan queue:work\` process and its process creation time. The existing \`scripts\\start-queue-worker.bat\` task remains the queue worker launcher.

## Sync notification

The unified \`scripts\\scheduler\\sync-all.php\` runner sends one consolidated \`azure.sync.completed\` event after all six modules complete and the HTML report has been written. Standalone module scripts do not send duplicate aggregate notifications.

## Template placeholders

Common fields: \`{event_key}\`, \`{event_label}\`, \`{is_test}\`, \`{test_timestamp}\`.

Server start:

- \`{server_boot_time}\`
- \`{iis_status}\`, \`{iis_started_at}\`
- \`{php_status}\`, \`{php_started_at}\`
- \`{mysql_status}\`, \`{mysql_started_at}\`
- \`{queue_status}\`, \`{queue_started_at}\`

Sync status:

- \`{overall_status}\`, \`{sync_started_at}\`, \`{completed_at}\`, \`{duration}\`
- \`{subscriptions_status}\`, \`{resources_status}\`, \`{costs_status}\`
- \`{security_status}\`, \`{alerts_status}\`, \`{web_urls_status}\`

Other application events expose the context passed by their event producer. If a placeholder is not available in the selected event context, it renders as \`Not available\`.
