<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MedicalDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalDocumentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MedicalDocument');
    }

    public function view(AuthUser $authUser, MedicalDocument $medicalDocument): bool
    {
        return $authUser->can('View:MedicalDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MedicalDocument');
    }

    public function update(AuthUser $authUser, MedicalDocument $medicalDocument): bool
    {
        return $authUser->can('Update:MedicalDocument');
    }

    public function delete(AuthUser $authUser, MedicalDocument $medicalDocument): bool
    {
        return $authUser->can('Delete:MedicalDocument');
    }

    public function restore(AuthUser $authUser, MedicalDocument $medicalDocument): bool
    {
        return $authUser->can('Restore:MedicalDocument');
    }

    public function forceDelete(AuthUser $authUser, MedicalDocument $medicalDocument): bool
    {
        return $authUser->can('ForceDelete:MedicalDocument');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MedicalDocument');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MedicalDocument');
    }

    public function replicate(AuthUser $authUser, MedicalDocument $medicalDocument): bool
    {
        return $authUser->can('Replicate:MedicalDocument');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MedicalDocument');
    }

}