<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HealthArticle;
use Illuminate\Auth\Access\HandlesAuthorization;

class HealthArticlePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HealthArticle');
    }

    public function view(AuthUser $authUser, HealthArticle $healthArticle): bool
    {
        return $authUser->can('View:HealthArticle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HealthArticle');
    }

    public function update(AuthUser $authUser, HealthArticle $healthArticle): bool
    {
        return $authUser->can('Update:HealthArticle');
    }

    public function delete(AuthUser $authUser, HealthArticle $healthArticle): bool
    {
        return $authUser->can('Delete:HealthArticle');
    }

    public function restore(AuthUser $authUser, HealthArticle $healthArticle): bool
    {
        return $authUser->can('Restore:HealthArticle');
    }

    public function forceDelete(AuthUser $authUser, HealthArticle $healthArticle): bool
    {
        return $authUser->can('ForceDelete:HealthArticle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HealthArticle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HealthArticle');
    }

    public function replicate(AuthUser $authUser, HealthArticle $healthArticle): bool
    {
        return $authUser->can('Replicate:HealthArticle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HealthArticle');
    }

}