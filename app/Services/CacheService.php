<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Cache Service Helper
 * 
 * Centralized cache management for consistent TTL and key naming.
 * Uses Redis for optimal performance.
 */
class CacheService
{
    /**
     * Cache Time-To-Live (TTL) in seconds
     */
    const TTL_SHORT = 60;           // 1 minute - for frequently changing data
    const TTL_MEDIUM = 300;         // 5 minutes - for moderately changing data
    const TTL_LONG = 600;           // 10 minutes - for rarely changing data
    const TTL_VERY_LONG = 3600;     // 1 hour - for static data
    const TTL_DAY = 86400;          // 24 hours - for very static data

    /**
     * Cache Key Prefixes
     */
    const PREFIX_DRUG = 'drugs:';
    const PREFIX_ARTICLE = 'articles:';
    const PREFIX_DOCTOR = 'doctors:';
    const PREFIX_USER = 'users:';

    /**
     * Cache drug categories
     */
    public static function drugCategories(): mixed
    {
        return Cache::remember(
            self::PREFIX_DRUG . 'categories',
            self::TTL_LONG,
            fn() => \App\Models\Drug::select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
        );
    }

    /**
     * Cache single drug detail
     */
    public static function drugDetail(int $drugId): mixed
    {
        return Cache::remember(
            self::PREFIX_DRUG . "detail:{$drugId}",
            self::TTL_MEDIUM,
            fn() => \App\Models\Drug::with(['prices' => fn($q) => $q->orderBy('price_min')])
                ->find($drugId)
        );
    }

    /**
     * Cache related drugs for a drug
     */
    public static function relatedDrugs(string $category, int $excludeId): mixed
    {
        $cacheKey = self::PREFIX_DRUG . "related:{$category}:{$excludeId}";
        
        return Cache::remember(
            $cacheKey,
            self::TTL_MEDIUM,
            fn() => \App\Models\Drug::where('category', $category)
                ->where('id', '!=', $excludeId)
                ->limit(4)
                ->get()
        );
    }

    /**
     * Cache article categories with counts
     */
    public static function articleCategories(): mixed
    {
        return Cache::remember(
            self::PREFIX_ARTICLE . 'categories',
            self::TTL_LONG,
            fn() => \App\Models\HealthArticle::where('verified', true)
                ->whereNotNull('published_at')
                ->selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray()
        );
    }

    /**
     * Cache featured article
     */
    public static function featuredArticle(): mixed
    {
        return Cache::remember(
            self::PREFIX_ARTICLE . 'featured',
            self::TTL_MEDIUM,
            fn() => \App\Models\HealthArticle::where('verified', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->first()
        );
    }

    /**
     * Cache popular articles
     */
    public static function popularArticles(int $limit = 3): mixed
    {
        return Cache::remember(
            self::PREFIX_ARTICLE . "popular:{$limit}",
            self::TTL_MEDIUM,
            fn() => \App\Models\HealthArticle::where('verified', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderBy('published_at', 'desc')
                ->skip(1)
                ->take($limit)
                ->get()
        );
    }

    /**
     * Cache single article detail
     */
    public static function articleDetail(string $slug): mixed
    {
        return Cache::remember(
            self::PREFIX_ARTICLE . "detail:{$slug}",
            self::TTL_MEDIUM,
            fn() => \App\Models\HealthArticle::where('slug', $slug)
                ->where('verified', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->first()
        );
    }

    /**
     * Cache related articles
     */
    public static function relatedArticles(string $category, int $excludeId, int $limit = 3): mixed
    {
        $cacheKey = self::PREFIX_ARTICLE . "related:{$category}:{$excludeId}:{$limit}";
        
        return Cache::remember(
            $cacheKey,
            self::TTL_MEDIUM,
            fn() => \App\Models\HealthArticle::where('verified', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->where('id', '!=', $excludeId)
                ->where('category', $category)
                ->take($limit)
                ->get()
        );
    }

    /**
     * Cache doctors list for patient selection
     */
    public static function doctorsList(array $excludeIds = []): mixed
    {
        // Create unique cache key based on excluded IDs
        $excludeKey = empty($excludeIds) ? 'all' : md5(implode(',', $excludeIds));
        
        return Cache::remember(
            self::PREFIX_DOCTOR . "list:{$excludeKey}",
            self::TTL_SHORT, // Short TTL because excluded IDs change per user
            fn() => \App\Models\User::role('doctor')
                ->select('id', 'name', 'email')
                ->whereNotIn('id', $excludeIds)
                ->with('profile:user_id,photo_profile')
                ->get()
                ->map(fn($doctor) => [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'email' => $doctor->email,
                    'profile' => $doctor->profile ? [
                        'photo_profile' => $doctor->profile->photo_profile,
                    ] : null,
                ])
        );
    }

    /**
     * Cache all doctors count
     */
    public static function doctorsCount(): int
    {
        return Cache::remember(
            self::PREFIX_DOCTOR . 'count',
            self::TTL_MEDIUM,
            fn() => \App\Models\User::role('doctor')->count()
        );
    }

    /**
     * Cache user profile data
     */
    public static function userProfile(int $userId): mixed
    {
        return Cache::remember(
            self::PREFIX_USER . "profile:{$userId}",
            self::TTL_SHORT,
            fn() => \App\Models\UserProfile::where('user_id', $userId)->first()
        );
    }

    /**
     * Invalidate drug-related caches
     */
    public static function invalidateDrugCache(?int $drugId = null): void
    {
        Cache::forget(self::PREFIX_DRUG . 'categories');
        
        if ($drugId) {
            Cache::forget(self::PREFIX_DRUG . "detail:{$drugId}");
        }
    }

    /**
     * Invalidate article-related caches
     */
    public static function invalidateArticleCache(?string $slug = null): void
    {
        Cache::forget(self::PREFIX_ARTICLE . 'categories');
        Cache::forget(self::PREFIX_ARTICLE . 'featured');
        Cache::forget(self::PREFIX_ARTICLE . 'popular:3');
        
        if ($slug) {
            Cache::forget(self::PREFIX_ARTICLE . "detail:{$slug}");
        }
    }

    /**
     * Invalidate doctor-related caches
     */
    public static function invalidateDoctorCache(): void
    {
        Cache::forget(self::PREFIX_DOCTOR . 'count');
        // Note: list caches with different excludeIds will expire naturally
    }

    /**
     * Invalidate user profile cache
     */
    public static function invalidateUserProfile(int $userId): void
    {
        Cache::forget(self::PREFIX_USER . "profile:{$userId}");
    }

    /**
     * Clear all application caches
     */
    public static function clearAll(): void
    {
        Cache::flush();
    }

    /**
     * Get cache statistics (for debugging)
     */
    public static function getStats(): array
    {
        $redis = Cache::getRedis();
        
        if ($redis) {
            $info = $redis->info();
            return [
                'driver' => 'redis',
                'connected_clients' => $info['connected_clients'] ?? 'N/A',
                'used_memory' => $info['used_memory_human'] ?? 'N/A',
                'total_keys' => $redis->dbSize() ?? 'N/A',
                'hits' => $info['keyspace_hits'] ?? 'N/A',
                'misses' => $info['keyspace_misses'] ?? 'N/A',
            ];
        }

        return ['driver' => config('cache.default')];
    }
}
