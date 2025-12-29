<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DrugController extends Controller
{
   /**
    * Display drug catalog list
    */
   public function index(Request $request)
   {
      $query = Drug::with('prices');

      // Search
      if ($search = $request->input('search')) {
         $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
               ->orWhere('category', 'like', "%{$search}%")
               ->orWhere('description', 'like', "%{$search}%");
         });
      }

      // Category filter
      if ($category = $request->input('category')) {
         $query->where('category', $category);
      }

      // Pregnancy safe filter
      if ($request->has('pregnancy_safe') && $request->input('pregnancy_safe') !== '') {
         $query->where('pregnancy_safe', (bool) $request->input('pregnancy_safe'));
      }

      // Get all unique categories - cached with Redis
      $categories = CacheService::drugCategories();

      // Paginate results
      $drugs = $query->orderBy('name')
         ->paginate(12)
         ->withQueryString();

      return Inertia::render('DrugCatalog', [
         'drugs' => $drugs,
         'categories' => $categories,
         'filters' => [
            'search' => $request->input('search', ''),
            'category' => $request->input('category', ''),
            'pregnancy_safe' => $request->input('pregnancy_safe', ''),
         ],
      ]);
   }

   /**
    * Display drug detail
    */
   public function show(Drug $drug)
   {
      $drug->load([
         'prices' => function ($query) {
            $query->orderBy('price_min');
         }
      ]);

      // Get related drugs from same category - cached with Redis
      $relatedDrugs = CacheService::relatedDrugs($drug->category, $drug->id);

      return Inertia::render('DrugDetail', [
         'drug' => $drug,
         'relatedDrugs' => $relatedDrugs,
      ]);
   }
}
