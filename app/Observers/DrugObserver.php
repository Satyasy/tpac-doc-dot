<?php

namespace App\Observers;

use App\Models\Drug;
use App\Services\CacheService;

/**
 * Drug Model Observer
 * 
 * Handles cache invalidation when drugs are created, updated, or deleted.
 */
class DrugObserver
{
   /**
    * Handle the Drug "created" event.
    */
   public function created(Drug $drug): void
   {
      CacheService::invalidateDrugCache();
   }

   /**
    * Handle the Drug "updated" event.
    */
   public function updated(Drug $drug): void
   {
      CacheService::invalidateDrugCache($drug->id);
   }

   /**
    * Handle the Drug "deleted" event.
    */
   public function deleted(Drug $drug): void
   {
      CacheService::invalidateDrugCache($drug->id);
   }
}
