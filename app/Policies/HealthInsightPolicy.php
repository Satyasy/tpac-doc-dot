<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HealthInsight;
use Illuminate\Auth\Access\HandlesAuthorization;

class HealthInsightPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HealthInsight');
    }

    public function view(AuthUser $authUser, HealthInsight $healthInsight): bool
    {
        return $authUser->can('View:HealthInsight');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HealthInsight');
    }

    public function update(AuthUser $authUser, HealthInsight $healthInsight): bool
    {
        return $authUser->can('Update:HealthInsight');
    }

    public function delete(AuthUser $authUser, HealthInsight $healthInsight): bool
    {
        return $authUser->can('Delete:HealthInsight');
    }

    public function restore(AuthUser $authUser, HealthInsight $healthInsight): bool
    {
        return $authUser->can('Restore:HealthInsight');
    }

    public function forceDelete(AuthUser $authUser, HealthInsight $healthInsight): bool
    {
        return $authUser->can('ForceDelete:HealthInsight');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HealthInsight');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HealthInsight');
    }

    public function replicate(AuthUser $authUser, HealthInsight $healthInsight): bool
    {
        return $authUser->can('Replicate:HealthInsight');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HealthInsight');
    }

}