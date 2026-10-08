<?php

namespace App\Enums;

enum UserRole: string
{
    case GlobalOwner = 'global_owner';
    case GlobalContributor = 'global_contributor';
    case GlobalReader = 'global_reader';
    case RestrictedOwner = 'restricted_owner';
    case RestrictedContributor = 'restricted_contributor';
    case RestrictedReader = 'restricted_reader';

    public function label(): string
    {
        return match ($this) {
            self::GlobalOwner => 'Global Owner',
            self::GlobalContributor => 'Global Contributor',
            self::GlobalReader => 'Global Reader',
            self::RestrictedOwner => 'Restricted Owner',
            self::RestrictedContributor => 'Restricted Contributor',
            self::RestrictedReader => 'Restricted Reader',
        };
    }

    public function isGlobal(): bool
    {
        return match ($this) {
            self::GlobalOwner, self::GlobalContributor, self::GlobalReader => true,
            default => false,
        };
    }

    public function isRestricted(): bool
    {
        return ! $this->isGlobal();
    }
}
