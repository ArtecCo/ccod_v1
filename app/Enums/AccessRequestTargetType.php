<?php

enum AccessRequestTargetType: string
{
    case Team = 'team';
    case Subscription = 'subscription';
    case UserGroup = 'user_group';

    public function label(): string
    {
        return match ($this) {
            self::Team => 'Team',
            self::Subscription => 'Subscription',
            self::UserGroup => 'User Group',
        };
    }
}
