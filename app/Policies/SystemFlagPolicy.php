<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SystemFlag;
use Illuminate\Auth\Access\HandlesAuthorization;

class SystemFlagPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SystemFlag');
    }

    public function view(AuthUser $authUser, SystemFlag $systemFlag): bool
    {
        return $authUser->can('View:SystemFlag');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SystemFlag');
    }

    public function update(AuthUser $authUser, SystemFlag $systemFlag): bool
    {
        return $authUser->can('Update:SystemFlag');
    }

    public function delete(AuthUser $authUser, SystemFlag $systemFlag): bool
    {
        return $authUser->can('Delete:SystemFlag');
    }

    public function restore(AuthUser $authUser, SystemFlag $systemFlag): bool
    {
        return $authUser->can('Restore:SystemFlag');
    }

    public function forceDelete(AuthUser $authUser, SystemFlag $systemFlag): bool
    {
        return $authUser->can('ForceDelete:SystemFlag');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SystemFlag');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SystemFlag');
    }

    public function replicate(AuthUser $authUser, SystemFlag $systemFlag): bool
    {
        return $authUser->can('Replicate:SystemFlag');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SystemFlag');
    }

}