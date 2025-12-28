<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MentalHealthLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class MentalHealthLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MentalHealthLog');
    }

    public function view(AuthUser $authUser, MentalHealthLog $mentalHealthLog): bool
    {
        return $authUser->can('View:MentalHealthLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MentalHealthLog');
    }

    public function update(AuthUser $authUser, MentalHealthLog $mentalHealthLog): bool
    {
        return $authUser->can('Update:MentalHealthLog');
    }

    public function delete(AuthUser $authUser, MentalHealthLog $mentalHealthLog): bool
    {
        return $authUser->can('Delete:MentalHealthLog');
    }

    public function restore(AuthUser $authUser, MentalHealthLog $mentalHealthLog): bool
    {
        return $authUser->can('Restore:MentalHealthLog');
    }

    public function forceDelete(AuthUser $authUser, MentalHealthLog $mentalHealthLog): bool
    {
        return $authUser->can('ForceDelete:MentalHealthLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MentalHealthLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MentalHealthLog');
    }

    public function replicate(AuthUser $authUser, MentalHealthLog $mentalHealthLog): bool
    {
        return $authUser->can('Replicate:MentalHealthLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MentalHealthLog');
    }

}