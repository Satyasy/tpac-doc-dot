<?php

namespace App\Observers;

use App\Models\HealthArticle;
use App\Services\CacheService;

/**
 * HealthArticle Model Observer
 * 
 * Handles cache invalidation when articles are created, updated, or deleted.
 */
class HealthArticleObserver
{
   /**
    * Handle the HealthArticle "created" event.
    */
   public function created(HealthArticle $article): void
   {
      CacheService::invalidateArticleCache();
   }

   /**
    * Handle the HealthArticle "updated" event.
    */
   public function updated(HealthArticle $article): void
   {
      CacheService::invalidateArticleCache($article->slug);
   }

   /**
    * Handle the HealthArticle "deleted" event.
    */
   public function deleted(HealthArticle $article): void
   {
      CacheService::invalidateArticleCache($article->slug);
   }
}
