<?php

namespace App\Http\Controllers;

use App\Models\PhysicalHealthLog;
use App\Models\MentalHealthLog;
use App\Models\HealthInsight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class HealthDashboardController extends Controller
{
   /**
    * Display health dashboard
    */
   public function index()
   {
      $user = Auth::user();
      $profile = $user->profile;

      // Get recent physical health logs
      $physicalLogs = PhysicalHealthLog::where('user_id', $user->id)
         ->orderBy('logged_at', 'desc')
         ->take(10)
         ->get();

      // Get recent mental health logs
      $mentalLogs = MentalHealthLog::where('user_id', $user->id)
         ->orderBy('logged_at', 'desc')
         ->take(10)
         ->get();

      // Get health insights
      $insights = HealthInsight::where('user_id', $user->id)
         ->orderBy('created_at', 'desc')
         ->take(5)
         ->get();

      // Calculate stats
      $weekAgo = Carbon::now()->subWeek();

      $avgMood = MentalHealthLog::where('user_id', $user->id)
         ->where('logged_at', '>=', $weekAgo)
         ->avg('mood') ?? 0;

      $avgStress = MentalHealthLog::where('user_id', $user->id)
         ->where('logged_at', '>=', $weekAgo)
         ->avg('stress_level') ?? 0;

      $avgSleep = MentalHealthLog::where('user_id', $user->id)
         ->where('logged_at', '>=', $weekAgo)
         ->avg('sleep_hours') ?? 0;

      $totalActivityMinutes = PhysicalHealthLog::where('user_id', $user->id)
         ->where('logged_at', '>=', $weekAgo)
         ->sum('activity_minutes') ?? 0;

      $logsThisWeek = PhysicalHealthLog::where('user_id', $user->id)
         ->where('logged_at', '>=', $weekAgo)
         ->count() + MentalHealthLog::where('user_id', $user->id)
            ->where('logged_at', '>=', $weekAgo)
            ->count();

      return Inertia::render('HealthDashboard', [
         'profile' => $profile ? [
            'height' => $profile->height,
            'weight' => $profile->weight,
            'bmi' => $profile->bmi,
            'bmi_category' => $profile->bmi_category,
         ] : null,
         'physicalLogs' => $physicalLogs,
         'mentalLogs' => $mentalLogs,
         'insights' => $insights,
         'stats' => [
            'avg_mood' => round($avgMood, 1),
            'avg_stress' => round($avgStress, 1),
            'avg_sleep' => round($avgSleep, 1),
            'total_activity_minutes' => $totalActivityMinutes,
            'logs_this_week' => $logsThisWeek,
         ],
      ]);
   }

   /**
    * Store physical health log
    */
   public function storePhysical(Request $request)
   {
      $request->validate([
         'weight_kg' => 'nullable|numeric|min:1|max:500',
         'blood_pressure' => 'nullable|string|max:20',
         'activity_minutes' => 'nullable|integer|min:0|max:1440',
         'logged_at' => 'required|date',
      ]);

      PhysicalHealthLog::create([
         'user_id' => Auth::id(),
         'weight_kg' => $request->weight_kg ?: null,
         'blood_pressure' => $request->blood_pressure ?: null,
         'activity_minutes' => $request->activity_minutes ?: null,
         'logged_at' => $request->logged_at,
      ]);

      return back()->with('success', 'Log kesehatan fisik berhasil disimpan.');
   }

   /**
    * Store mental health log
    */
   public function storeMental(Request $request)
   {
      $request->validate([
         'mood' => 'required|integer|min:1|max:5',
         'stress_level' => 'required|integer|min:1|max:5',
         'sleep_hours' => 'nullable|numeric|min:0|max:24',
         'note' => 'nullable|string|max:500',
         'logged_at' => 'required|date',
      ]);

      MentalHealthLog::create([
         'user_id' => Auth::id(),
         'mood' => $request->mood,
         'stress_level' => $request->stress_level,
         'sleep_hours' => $request->sleep_hours ?: null,
         'note' => $request->note ?: null,
         'logged_at' => $request->logged_at,
      ]);

      return back()->with('success', 'Log kesehatan mental berhasil disimpan.');
   }

   /**
    * Store mental health log via API (from mood survey)
    */
   public function storeMentalApi(Request $request)
   {
      $request->validate([
         'mood' => 'required|integer|min:1|max:5',
         'stress_level' => 'required|integer|min:1|max:5',
         'sleep_hours' => 'nullable|numeric|min:0|max:24',
         'note' => 'nullable|string|max:1000',
      ]);

      $log = MentalHealthLog::create([
         'user_id' => Auth::id(),
         'mood' => $request->mood,
         'stress_level' => $request->stress_level,
         'sleep_hours' => $request->sleep_hours ?: null,
         'note' => $request->note ?: null,
         'logged_at' => now(),
      ]);

      return response()->json([
         'success' => true,
         'message' => 'Log kesehatan mental berhasil disimpan.',
         'data' => $log,
      ]);
   }

   /**
    * Store physical health log via API
    */
   public function storePhysicalApi(Request $request)
   {
      $request->validate([
         'weight_kg' => 'nullable|numeric|min:1|max:500',
         'blood_pressure' => 'nullable|string|max:20',
         'activity_minutes' => 'nullable|integer|min:0|max:1440',
      ]);

      $log = PhysicalHealthLog::create([
         'user_id' => Auth::id(),
         'weight_kg' => $request->weight_kg ?: null,
         'blood_pressure' => $request->blood_pressure ?: null,
         'activity_minutes' => $request->activity_minutes ?: null,
         'logged_at' => now(),
      ]);

      return response()->json([
         'success' => true,
         'message' => 'Log kesehatan fisik berhasil disimpan.',
         'data' => $log,
      ]);
   }
}
