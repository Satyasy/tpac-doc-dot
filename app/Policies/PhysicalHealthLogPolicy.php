<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PhysicalHealthLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhysicalHealthLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PhysicalHealthLog');
    }

    public function view(AuthUser $authUser, PhysicalHealthLog $physicalHealthLog): bool
    {
        return $authUser->can('View:PhysicalHealthLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PhysicalHealthLog');
    }

    public function update(AuthUser $authUser, PhysicalHealthLog $physicalHealthLog): bool
    {
        return $authUser->can('Update:PhysicalHealthLog');
    }

    public function delete(AuthUser $authUser, PhysicalHealthLog $physicalHealthLog): bool
    {
        return $authUser->can('Delete:PhysicalHealthLog');
    }

    public function restore(AuthUser $authUser, PhysicalHealthLog $physicalHealthLog): bool
    {
        return $authUser->can('Restore:PhysicalHealthLog');
    }

    public function forceDelete(AuthUser $authUser, PhysicalHealthLog $physicalHealthLog): bool
    {
        return $authUser->can('ForceDelete:PhysicalHealthLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PhysicalHealthLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PhysicalHealthLog');
    }

    public function replicate(AuthUser $authUser, PhysicalHealthLog $physicalHealthLog): bool
    {
        return $authUser->can('Replicate:PhysicalHealthLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PhysicalHealthLog');
    }

}