<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AiAuditLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class AiAuditLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AiAuditLog');
    }

    public function view(AuthUser $authUser, AiAuditLog $aiAuditLog): bool
    {
        return $authUser->can('View:AiAuditLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AiAuditLog');
    }

    public function update(AuthUser $authUser, AiAuditLog $aiAuditLog): bool
    {
        return $authUser->can('Update:AiAuditLog');
    }

    public function delete(AuthUser $authUser, AiAuditLog $aiAuditLog): bool
    {
        return $authUser->can('Delete:AiAuditLog');
    }

    public function restore(AuthUser $authUser, AiAuditLog $aiAuditLog): bool
    {
        return $authUser->can('Restore:AiAuditLog');
    }

    public function forceDelete(AuthUser $authUser, AiAuditLog $aiAuditLog): bool
    {
        return $authUser->can('ForceDelete:AiAuditLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AiAuditLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AiAuditLog');
    }

    public function replicate(AuthUser $authUser, AiAuditLog $aiAuditLog): bool
    {
        return $authUser->can('Replicate:AiAuditLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AiAuditLog');
    }

}