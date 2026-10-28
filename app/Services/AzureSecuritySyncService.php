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
| extend resourceId = coalesce(
    tostring(properties.resourceDetails.id),
    tostring(properties.resourceDetails.Id),
    tostring(properties.resourceDetails.resourceId),
    tostring(properties.resourceDetails.ResourceId)
)
| extend recommendationName = coalesce(tostring(properties.displayName), tostring(properties.metadata.displayName))
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
| extend resourceSource = coalesce(tostring(properties.resourceDetails.source), tostring(properties.resourceDetails.Source))
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
        $properties = is_array($assessment['properties'] ?? null) ? $assessment['properties'] : [];
        $resourceDetails = is_array($assessment['resourceDetails'] ?? null)
            ? $assessment['resourceDetails']
            : (is_array($properties['resourceDetails'] ?? null) ? $properties['resourceDetails'] : []);

        $resourceId = $this->firstString([
            $assessment['resourceId'] ?? null,
            $resourceDetails['id'] ?? null,
            $resourceDetails['Id'] ?? null,
            $resourceDetails['resourceId'] ?? null,
            $resourceDetails['ResourceId'] ?? null,
            $resourceDetails['resourceID'] ?? null,
        ]);

        if ($resourceId === null && is_array($properties['additionalData'] ?? null)) {
            $additionalData = $properties['additionalData'];
            $resourceId = $this->firstString([
                $additionalData['AzureResourceId'] ?? null,
                $additionalData['azureResourceId'] ?? null,
                $additionalData['resourceId'] ?? null,
                $additionalData['resourceID'] ?? null,
            ]);
        }

        $metadata = is_array($properties['metadata'] ?? null) ? $properties['metadata'] : [];

        $attributes = [
            'assessment_id' => (string) $assessment['id'],
            'subscription_id' => $subscriptionId,
            'resource_id' => $resourceId,
            'recommendation_id' => $this->stringOrNull($assessment['name'] ?? null),
            'recommendation_name' => $this->firstString([
                $assessment['recommendationName'] ?? null,
                $assessment['name'] ?? null,
                $metadata['displayName'] ?? null,
            ]),
            'state' => $this->firstString([$assessment['statusCode'] ?? null, $properties['status']['code'] ?? null]) ?? 'Unhealthy',
            'severity' => $this->firstString([$assessment['severity'] ?? null, $metadata['severity'] ?? null]),
            'description' => $this->firstString([$assessment['description'] ?? null, $metadata['description'] ?? null]),
            'remediation_description' => $this->firstString([$assessment['remediationDescription'] ?? null, $metadata['remediationDescription'] ?? null]),
            'assessment_type' => $this->firstString([$assessment['assessmentType'] ?? null, $metadata['assessmentType'] ?? null]),
            'implementation_effort' => $this->firstString([$assessment['implementationEffort'] ?? null, $metadata['implementationEffort'] ?? null]),
            'resource_source' => $this->firstString([$assessment['resourceSource'] ?? null, $resourceDetails['source'] ?? null, $resourceDetails['Source'] ?? null]),
            'policy_definition_id' => $this->firstString([$assessment['policyDefinitionId'] ?? null, $metadata['policyDefinitionId'] ?? null]),
            'categories' => $this->metadataArray($metadata['categories'] ?? null),
            'threats' => $this->metadataArray($metadata['threats'] ?? null),
            'cves' => $this->extractCves($assessment, $metadata),
            'user_impact' => $this->firstString([$assessment['userImpact'] ?? null, $metadata['userImpact'] ?? null]),
            'status_cause' => $this->firstString([$assessment['statusCause'] ?? null, $properties['status']['cause'] ?? null]),
            'status_description' => $this->firstString([$assessment['statusDescription'] ?? null, $properties['status']['description'] ?? null]),
            'first_evaluated_at' => $this->dateOrNull($assessment['firstEvaluationDate'] ?? null),
            'status_changed_at' => $this->dateOrNull($assessment['statusChangeDate'] ?? null),
            'portal_link' => $this->firstString([$assessment['portalLink'] ?? null, $properties['links']['azurePortalUri'] ?? null]),
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

    private function extractCves(array $assessment, array $metadata): ?array
    {
        $values = [];
        foreach ([
            $metadata['cves'] ?? null,
            $metadata['CVEs'] ?? null,
            $metadata['cve'] ?? null,
            $metadata['CVE'] ?? null,
        ] as $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    if (is_string($item)) {
                        $values[] = $item;
                    } elseif (is_array($item)) {
                        foreach (['cveId', 'CveId', 'id', 'name'] as $key) {
                            if (is_string($item[$key] ?? null)) {
                                $values[] = $item[$key];
                                break;
                            }
                        }
                    }
                }
            } elseif (is_string($value)) {
                $values[] = $value;
            }
        }

        $json = json_encode($assessment['properties'] ?? null, JSON_UNESCAPED_SLASHES);
        if (is_string($json)) {
            preg_match_all('/CVE-\d{4}-\d{4,7}/i', $json, $matches);
            $values = array_merge($values, $matches[0] ?? []);
        }

        $cves = [];
        foreach ($values as $value) {
            preg_match_all('/CVE-\d{4}-\d{4,7}/i', $value, $matches);
            foreach ($matches[0] ?? [] as $cve) {
                $cves[] = strtoupper($cve);
            }
        }

        $cves = array_values(array_unique($cves));
        return $cves === [] ? null : $cves;
    }

    private function metadataArray(mixed $value): ?array
    {
        if (! is_array($value)) {
            return null;
        }

        return array_values($value);
    }

    private function firstString(array $values): ?string
    {
        foreach ($values as $value) {
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
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
