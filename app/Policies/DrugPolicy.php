<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Drug;
use Illuminate\Auth\Access\HandlesAuthorization;

class DrugPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Drug');
    }

    public function view(AuthUser $authUser, Drug $drug): bool
    {
        return $authUser->can('View:Drug');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Drug');
    }

    public function update(AuthUser $authUser, Drug $drug): bool
    {
        return $authUser->can('Update:Drug');
    }

    public function delete(AuthUser $authUser, Drug $drug): bool
    {
        return $authUser->can('Delete:Drug');
    }

    public function restore(AuthUser $authUser, Drug $drug): bool
    {
        return $authUser->can('Restore:Drug');
    }

    public function forceDelete(AuthUser $authUser, Drug $drug): bool
    {
        return $authUser->can('ForceDelete:Drug');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Drug');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Drug');
    }

    public function replicate(AuthUser $authUser, Drug $drug): bool
    {
        return $authUser->can('Replicate:Drug');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Drug');
    }

}