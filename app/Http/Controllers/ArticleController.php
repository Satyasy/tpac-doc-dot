<?php

namespace App\Http\Controllers;

use App\Models\HealthArticle;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleController extends Controller
{
   /**
    * Display listing of articles
    */
   public function index(Request $request)
   {
      $query = HealthArticle::query()
         ->where('verified', true)
         ->whereNotNull('published_at')
         ->where('published_at', '<=', now());

      // Filter by category
      if ($request->filled('category')) {
         $query->where('category', $request->category);
      }

      // Search
      if ($request->filled('search')) {
         $search = $request->search;
         $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
               ->orWhere('content', 'like', "%{$search}%");
         });
      }

      $articles = $query->orderBy('published_at', 'desc')
         ->paginate(12)
         ->withQueryString();

      // Get featured article - cached with Redis
      $featuredArticle = CacheService::featuredArticle();

      // Get popular articles - cached with Redis
      $popularArticles = CacheService::popularArticles(3);

      // Get categories with counts - cached with Redis
      $categories = CacheService::articleCategories();

      return Inertia::render('Article', [
         'articles' => $articles,
         'featuredArticle' => $featuredArticle,
         'popularArticles' => $popularArticles,
         'categories' => $categories,
         'currentCategory' => $request->category,
         'searchQuery' => $request->search,
      ]);
   }

   /**
    * Display single article
    */
   public function show(string $slug)
   {
      // Get article (not cached because needs 404 handling)
      $article = HealthArticle::where('slug', $slug)
         ->where('verified', true)
         ->whereNotNull('published_at')
         ->where('published_at', '<=', now())
         ->firstOrFail();

      // Get related articles - cached with Redis
      $relatedArticles = CacheService::relatedArticles(
         $article->category,
         $article->id,
         3
      );

      return Inertia::render('ArticleDetail', [
         'article' => $article,
         'relatedArticles' => $relatedArticles,
      ]);
   }
}
