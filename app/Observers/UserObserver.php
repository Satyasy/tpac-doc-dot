<?php

namespace App\Observers;

use App\Models\User;
use App\Services\CacheService;

/**
 * User Model Observer
 * 
 * Handles cache invalidation when users (especially doctors) are updated.
 */
class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // If new doctor is created, invalidate doctor cache
        if ($user->hasRole('doctor')) {
            CacheService::invalidateDoctorCache();
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Invalidate user profile cache
        CacheService::invalidateUserProfile($user->id);
        
        // If doctor role changed, invalidate doctor cache
        if ($user->hasRole('doctor')) {
            CacheService::invalidateDoctorCache();
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        CacheService::invalidateUserProfile($user->id);
        
        if ($user->hasRole('doctor')) {
            CacheService::invalidateDoctorCache();
        }
    }
}
