<?php

declare(strict_types=1);

require __DIR__ . '/azure_client.php';

try {
    echo "CCOD Azure connection test\n";
    echo "==========================\n";

    $tenantId = azureEnv('AZURE_TENANT_ID');
    $clientId = azureEnv('AZURE_CLIENT_ID');

    echo "Tenant ID : {$tenantId}\n";
    echo "Client ID : {$clientId}\n";
    echo "Secret    : configured\n\n";

    echo "Requesting Microsoft Entra access token... ";
    $token = azureAccessToken();
    echo "OK\n";

    echo "Testing Azure Resource Manager... ";
    $response = azureGet(
        'https://management.azure.com/subscriptions?api-version=2020-01-01',
        $token,
    );

    if ($response['status'] < 200 || $response['status'] >= 300) {
        echo "FAILED\n";
        azurePrintError($response);
        exit(1);
    }

    echo "OK (HTTP {$response['status']})\n";

    $subscriptions = $response['body']['value'] ?? [];
    echo "Subscriptions visible to this service principal: " . count($subscriptions) . "\n";
    echo "Connection test passed.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "FAILED\n{$e->getMessage()}\n");
    exit(1);
}
