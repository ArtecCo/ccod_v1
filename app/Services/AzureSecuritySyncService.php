<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AzureSecurityAssessment;
use App\Models\AzureSubscription;
use Illuminate\Support\Carbon;
use RuntimeException;

class AzureSecuritySyncService
{
    private const API_VERSION = '2024-04-01';
    private const PAGE_SIZE = 1000;

    public function __construct(
        private readonly AzureSubscriptionSyncService $azure,
    ) {
    }

    public function sync(?string $subscriptionId = null): array
    {
        $subscriptions = AzureSubscription::query()
            ->when($subscriptionId !== null, fn ($query) => $query->where('subscription_id', $subscriptionId))
            ->pluck('subscription_id')
            ->filter(fn (mixed $id): bool => is_string($id) && $id !== '')
            ->values()
            ->all();

        $stats = [
            'subscriptions' => count($subscriptions),
            'discovered' => 0,
            'created' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'deleted' => 0,
        ];

        foreach ($subscriptions as $id) {
            $result = $this->syncSubscription($id);
            foreach ($result as $key => $value) {
                $stats[$key] += $value;
            }
        }

        return $stats;
    }

    private function syncSubscription(string $subscriptionId): array
    {
        $stats = ['discovered' => 0, 'created' => 0, 'updated' => 0, 'unchanged' => 0, 'deleted' => 0];
        $seen = [];
        $skipToken = null;

        do {
            $payload = [
                'subscriptions' => [$subscriptionId],
                'query' => <<<'KQL'
securityresources
| where type =~ "microsoft.security/assessments"
| where tostring(properties.status.code) =~ "Unhealthy"
| extend resourceDetails = properties.resourceDetails
| extend resourceId = tostring(properties.resourceDetails.Id)
| extend recommendationName = tostring(properties.displayName)
| extend statusCode = tostring(properties.status.code)
| extend statusCause = tostring(properties.status.cause)
| extend statusDescription = tostring(properties.status.description)
| extend firstEvaluationDate = todatetime(properties.status.firstEvaluationDate)
| extend statusChangeDate = todatetime(properties.status.statusChangeDate)
| extend severity = tostring(properties.metadata.severity)
| extend description = tostring(properties.metadata.description)
| extend remediationDescription = tostring(properties.metadata.remediationDescription)
| extend assessmentType = tostring(properties.metadata.assessmentType)
| extend implementationEffort = tostring(properties.metadata.implementationEffort)
| extend userImpact = tostring(properties.metadata.userImpact)
| extend resourceSource = tostring(properties.resourceDetails.Source)
| extend portalLink = tostring(properties.links.azurePortalUri)
| extend policyDefinitionId = tostring(properties.metadata.policyDefinitionId)
| project id, name, resourceId, resourceDetails, recommendationName, statusCode, statusCause, statusDescription, firstEvaluationDate, statusChangeDate, severity, description, remediationDescription, assessmentType, implementationEffort, userImpact, resourceSource, portalLink, policyDefinitionId, properties
| order by id asc
KQL,
                'options' => [
                    '$top' => self::PAGE_SIZE,
                    'resultFormat' => 'objectArray',
                ],
            ];

            if ($skipToken !== null) {
                $payload['options']['$skipToken'] = $skipToken;
            }

            $response = $this->azure->post(
                'https://management.azure.com/providers/Microsoft.ResourceGraph/resources?api-version=' . self::API_VERSION,
                $payload,
            )->throw();

            $page = $response->json();
            if (! is_array($page) || ! is_array($page['data'] ?? null)) {
                throw new RuntimeException('Invalid Azure security Resource Graph response.');
            }

            foreach ($page['data'] as $assessment) {
                if (! is_array($assessment) || ! is_string($assessment['id'] ?? null)) {
                    continue;
                }

                $stats['discovered']++;
                $assessmentId = $assessment['id'];
                $seen[] = $assessmentId;
                $stats[$this->syncOne($assessment, $subscriptionId)]++;
            }

            $next = $page['$skipToken'] ?? null;
            $skipToken = is_string($next) && $next !== '' ? $next : null;
        } while ($skipToken !== null);

        $query = AzureSecurityAssessment::query()->where('subscription_id', $subscriptionId);
        $stats['deleted'] = $seen === []
            ? $query->count()
            : (clone $query)->whereNotIn('assessment_id', $seen)->count();

        if ($seen === []) {
            $query->delete();
        } else {
            $query->whereNotIn('assessment_id', $seen)->delete();
        }

        return $stats;
    }

    private function syncOne(array $assessment, string $subscriptionId): string
    {
        $resourceId = $assessment['resourceId'] ?? null;
        $properties = is_array($assessment['properties'] ?? null) ? $assessment['properties'] : [];
        $resourceDetails = is_array($assessment['resourceDetails'] ?? null)
            ? $assessment['resourceDetails']
            : (is_array($properties['resourceDetails'] ?? null) ? $properties['resourceDetails'] : []);

        if (! is_string($resourceId) || $resourceId === '') {
            foreach (['id', 'Id'] as $key) {
                if (is_string($resourceDetails[$key] ?? null) && $resourceDetails[$key] !== '') {
                    $resourceId = $resourceDetails[$key];
                    break;
                }
            }
        }

        if (! is_string($resourceId) || $resourceId === '') {
            $additionalData = is_array($properties['additionalData'] ?? null) ? $properties['additionalData'] : [];
            foreach (['AzureResourceId', 'azureResourceId', 'resourceId'] as $key) {
                if (is_string($additionalData[$key] ?? null) && $additionalData[$key] !== '') {
                    $resourceId = $additionalData[$key];
                    break;
                }
            }
        }

        if (! is_string($resourceId) || $resourceId === '') {
            $resourceId = $this->resourceIdFromAssessmentId((string) $assessment['id']);
        }

        $attributes = [
            'assessment_id' => (string) $assessment['id'],
            'subscription_id' => $subscriptionId,
            'resource_id' => $this->stringOrNull($resourceId),
            'recommendation_id' => $this->stringOrNull($assessment['name'] ?? null),
            'recommendation_name' => $this->stringOrNull($assessment['recommendationName'] ?? null),
            'state' => 'Unhealthy',
            'severity' => $this->stringOrNull($assessment['severity'] ?? null),
            'description' => $this->stringOrNull($assessment['description'] ?? null),
            'remediation_description' => $this->stringOrNull($assessment['remediationDescription'] ?? null),
            'assessment_type' => $this->stringOrNull($assessment['assessmentType'] ?? null),
            'implementation_effort' => $this->stringOrNull($assessment['implementationEffort'] ?? null),
            'resource_source' => $this->stringOrNull($assessment['resourceSource'] ?? null),
            'policy_definition_id' => $this->stringOrNull($assessment['policyDefinitionId'] ?? null),
            'categories' => $this->metadataArray($assessment, 'categories'),
            'threats' => $this->metadataArray($assessment, 'threats'),
            'cves' => $this->extractCves($assessment),
            'user_impact' => $this->stringOrNull($assessment['userImpact'] ?? null),
            'status_cause' => $this->stringOrNull($assessment['statusCause'] ?? null),
            'status_description' => $this->stringOrNull($assessment['statusDescription'] ?? null),
            'first_evaluated_at' => $this->dateOrNull($assessment['firstEvaluationDate'] ?? null),
            'status_changed_at' => $this->dateOrNull($assessment['statusChangeDate'] ?? null),
            'portal_link' => $this->stringOrNull($assessment['portalLink'] ?? null),
            'synced_at' => now(),
        ];

        $existing = AzureSecurityAssessment::query()->where('assessment_id', $attributes['assessment_id'])->first();
        if ($existing === null) {
            AzureSecurityAssessment::query()->create($attributes);
            return 'created';
        }

        $changed = false;
        foreach ($attributes as $key => $value) {
            if ($key === 'synced_at') {
                continue;
            }
            if ($existing->getAttribute($key) != $value) {
                $changed = true;
                break;
            }
        }

        $existing->forceFill($attributes)->saveQuietly();
        return $changed ? 'updated' : 'unchanged';
    }

    private function resourceIdFromAssessmentId(string $assessmentId): ?string
    {
        $marker = '/providers/Microsoft.Security/assessments/';
        $position = stripos($assessmentId, $marker);

        if ($position === false) {
            return null;
        }

        $resourceId = substr($assessmentId, 0, $position);
        return $resourceId !== '' ? $resourceId : null;
    }

    private function metadataArray(array $assessment, string $key): ?array
    {
        $properties = $assessment['properties'] ?? null;
        $metadata = is_array($properties) ? ($properties['metadata'] ?? null) : null;
        $value = is_array($metadata) ? ($metadata[$key] ?? null) : null;

        return is_array($value) ? array_values($value) : null;
    }

    private function extractCves(array $assessment): ?array
    {
        $json = json_encode($assessment['properties'] ?? null, JSON_UNESCAPED_SLASHES);
        if (! is_string($json)) {
            return null;
        }

        preg_match_all('/CVE-\d{4}-\d{4,7}/i', $json, $matches);
        $cves = array_values(array_unique(array_map('strtoupper', $matches[0] ?? [])));

        return $cves === [] ? null : $cves;
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }

    private function dateOrNull(mixed $value): ?Carbon
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
