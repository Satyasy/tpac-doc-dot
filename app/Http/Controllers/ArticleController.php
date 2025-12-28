<?php

namespace App\Http\Controllers;

use App\Models\HealthArticle;
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

      // Get featured article (latest) - cached for 5 minutes
      $featuredArticle = cache()->remember('featured_article', 300, function () {
         return HealthArticle::where('verified', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->first();
      });

      // Get popular articles (recent ones, cached)
      $popularArticles = cache()->remember('popular_articles', 300, function () {
         return HealthArticle::where('verified', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->skip(1)
            ->take(3)
            ->get();
      });

      // Get categories with counts - cached for 10 minutes
      $categories = cache()->remember('article_categories', 600, function () {
         return HealthArticle::where('verified', true)
            ->whereNotNull('published_at')
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
      });

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
      $article = HealthArticle::where('slug', $slug)
         ->where('verified', true)
         ->whereNotNull('published_at')
         ->where('published_at', '<=', now())
         ->firstOrFail();

      // Get related articles
      $relatedArticles = HealthArticle::where('verified', true)
         ->whereNotNull('published_at')
         ->where('published_at', '<=', now())
         ->where('id', '!=', $article->id)
         ->where('category', $article->category)
         ->take(3)
         ->get();

      return Inertia::render('ArticleDetail', [
         'article' => $article,
         'relatedArticles' => $relatedArticles,
      ]);
   }
}
