# Microsoft Teams webhook notifications

## Configure destinations

In Developer Panel → Notifications → Teams Webhooks, create one or more destinations for each category:

- **Server start** reports Windows boot time, IIS (W3SVC/WAS), PHP FastCGI worker detection, MySQL2610, and the Laravel queue worker.
- **Sync status** reports the overall result and the status of Subscriptions, Resources, Costs, Security, Alerts, and Web URLs.

Webhook URLs are encrypted using Laravel's application key. Never place webhook URLs in source control or logs. Use a Teams Workflows incoming webhook URL configured for the intended channel or chat. Do not use the retired Office 365 Connector webhook type: Microsoft retired connector-based incoming webhooks in May 2026. See [Microsoft's current incoming webhook guidance](https://learn.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook).

Channel destinations support Adaptive Cards or legacy MessageCard payloads. Chat destinations use plain text with Teams-supported Markdown; arbitrary font sizes and text colors are not supported.

## Windows startup task

After pulling the repository, open Command Prompt as Administrator and run scripts\scheduler\register-server-start-task.cmd from the application root. It registers a Windows startup task running as SYSTEM, with a two-minute delay, using C:\PHP\php.exe and scripts\scheduler\notify-server-start.php.

The task needs read access to the application files and access to the local database. The delay allows IIS, MySQL, and the queue task to initialize before the status snapshot is sent. The script reports the state it observes; a component that is still starting may be shown as not running. PHP status is based on an active php-cgi.exe FastCGI worker, so it can report NOT DETECTED if IIS has not yet handled a PHP request after boot. IIS service start time is derived from its hosting process and can be approximate when Windows shares the service host process.

## Queue worker

The queue worker is detected from the running php.exe artisan queue:work process and its process creation time. The existing scripts\start-queue-worker.bat task remains the queue worker launcher.

## Sync notification

The unified scripts\scheduler\sync-all.php runner sends one consolidated notification after all six modules complete and the HTML report has been written. Standalone module scripts do not send duplicate aggregate notifications.

## Template placeholders

Server start:

- {server_boot_time}
- {iis_status}, {iis_started_at}
- {php_status}, {php_started_at}
- {mysql_status}, {mysql_started_at}
- {queue_status}, {queue_started_at}

Sync status:

- {overall_status}, {completed_at}
- {subscriptions_status}, {resources_status}, {costs_status}
- {security_status}, {alerts_status}, {web_urls_status}

If a placeholder is not available in the selected context, it remains visible as written.
