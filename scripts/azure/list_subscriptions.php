<?php

declare(strict_types=1);

require __DIR__ . '/azure_client.php';

try {
    $token = azureAccessToken();
    $url = 'https://management.azure.com/subscriptions?api-version=2020-01-01';
    $response = azureGet($url, $token);

    if ($response['status'] < 200 || $response['status'] >= 300) {
        azurePrintError($response);
        exit(1);
    }

    $subscriptions = $response['body']['value'] ?? [];

    echo "Azure subscriptions\n";
    echo "====================\n\n";

    if ($subscriptions === []) {
        echo "No subscriptions are visible to this service principal.\n";
        exit(0);
    }

    printf("%-38s %-35s %-12s %-12s\n", 'SUBSCRIPTION ID', 'DISPLAY NAME', 'STATE', 'TENANT');
    echo str_repeat('-', 105) . "\n";

    foreach ($subscriptions as $subscription) {
        printf(
            "%-38s %-35s %-12s %-12s\n",
            (string) ($subscription['subscriptionId'] ?? '—'),
            mb_strimwidth((string) ($subscription['displayName'] ?? '—'), 0, 35, '…'),
            (string) ($subscription['state'] ?? '—'),
            (string) ($subscription['tenantId'] ?? '—'),
        );
    }

    echo "\nTotal: " . count($subscriptions) . " subscription(s)\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Failed to list subscriptions: {$e->getMessage()}\n");
    exit(1);
}
