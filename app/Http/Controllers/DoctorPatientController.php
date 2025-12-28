<?php

namespace App\Http\Controllers;

use App\Models\DoctorPatient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorPatientController extends Controller
{
   /**
    * Get list of doctors (for patient to choose)
    */
   public function getDoctors()
   {
      $user = Auth::user();

      // Get IDs of doctors the user already has a relationship with
      $existingDoctorIds = [];
      if ($user) {
         $existingDoctorIds = DoctorPatient::where('patient_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->pluck('doctor_id')
            ->toArray();
      }

      $doctors = User::role('doctor')
         ->select('id', 'name', 'email')
         ->whereNotIn('id', $existingDoctorIds)
         ->with('profile:user_id,photo_profile')
         ->get()
         ->map(function ($doctor) {
            return [
               'id' => $doctor->id,
               'name' => $doctor->name,
               'email' => $doctor->email,
               'profile' => $doctor->profile ? [
                  'photo_profile' => $doctor->profile->photo_profile,
               ] : null,
            ];
         });

      return response()->json(['doctors' => $doctors]);
   }

   /**
    * Patient sends request to doctor
    */
   public function sendRequest(Request $request)
   {
      $request->validate([
         'doctor_id' => 'required|exists:users,id',
         'message' => 'nullable|string|max:500',
      ]);

      $user = Auth::user();
      $doctorId = $request->doctor_id;

      // Can't send request to yourself
      if ($doctorId == $user->id) {
         return response()->json(['message' => 'Tidak bisa mengirim permintaan ke diri sendiri 😅'], 400);
      }

      // Check if doctor exists and has doctor role
      $doctor = User::find($doctorId);
      if (!$doctor || !$doctor->isDoctor()) {
         return response()->json(['error' => 'Dokter tidak ditemukan'], 404);
      }

      // Check if relationship already exists
      $existing = DoctorPatient::where('doctor_id', $doctorId)
         ->where('patient_id', $user->id)
         ->first();

      if ($existing) {
         if ($existing->status === 'accepted') {
            return response()->json(['error' => 'Anda sudah terhubung dengan dokter ini'], 400);
         }
         if ($existing->status === 'pending') {
            return response()->json(['error' => 'Request Anda masih menunggu persetujuan'], 400);
         }
         // If rejected, allow re-request
         $existing->update([
            'status' => 'pending',
            'request_message' => $request->message,
            'response_message' => null,
            'rejected_at' => null,
         ]);
         return response()->json([
            'success' => true,
            'message' => 'Request berhasil dikirim ulang',
         ]);
      }

      DoctorPatient::create([
         'doctor_id' => $doctorId,
         'patient_id' => $user->id,
         'request_message' => $request->message,
      ]);

      return response()->json([
         'success' => true,
         'message' => 'Request berhasil dikirim ke dokter',
      ]);
   }

   /**
    * Get patient's doctors (for profile page)
    */
   public function getMyDoctors()
   {
      $user = Auth::user();

      $doctors = DoctorPatient::where('patient_id', $user->id)
         ->whereIn('status', ['pending', 'accepted'])
         ->with(['doctor:id,name,email', 'doctor.profile:user_id,photo_profile'])
         ->get()
         ->map(function ($relation) {
            return [
               'id' => $relation->id,
               'doctor_id' => $relation->doctor_id,
               'patient_id' => $relation->patient_id,
               'status' => $relation->status,
               'message' => $relation->request_message,
               'created_at' => $relation->created_at->toISOString(),
               'doctor' => [
                  'id' => $relation->doctor->id,
                  'name' => $relation->doctor->name,
                  'email' => $relation->doctor->email,
                  'profile' => $relation->doctor->profile ? [
                     'photo_profile' => $relation->doctor->profile->photo_profile,
                  ] : null,
               ],
            ];
         });

      return response()->json(['doctors' => $doctors]);
   }

   /**
    * Cancel pending request (patient)
    */
   public function cancelRequest(DoctorPatient $doctorPatient)
   {
      $user = Auth::user();

      if ($doctorPatient->patient_id !== $user->id) {
         return response()->json(['error' => 'Unauthorized'], 403);
      }

      if ($doctorPatient->status !== 'pending') {
         return response()->json(['error' => 'Hanya request pending yang bisa dibatalkan'], 400);
      }

      $doctorPatient->delete();

      return response()->json([
         'success' => true,
         'message' => 'Request berhasil dibatalkan',
      ]);
   }

   /**
    * Disconnect from doctor (patient)
    */
   public function disconnect(DoctorPatient $doctorPatient)
   {
      $user = Auth::user();

      if ($doctorPatient->patient_id !== $user->id) {
         return response()->json(['error' => 'Unauthorized'], 403);
      }

      $doctorPatient->delete();

      return response()->json([
         'success' => true,
         'message' => 'Berhasil disconnect dari dokter',
      ]);
   }

   // ============ DOCTOR ENDPOINTS ============

   /**
    * Get pending requests for doctor
    */
   public function getPendingRequests()
   {
      $user = Auth::user();

      if (!$user->isDoctor()) {
         return response()->json(['error' => 'Unauthorized'], 403);
      }

      $requests = DoctorPatient::where('doctor_id', $user->id)
         ->pending()
         ->with(['patient:id,name,email', 'patient.profile:user_id,photo_profile'])
         ->latest()
         ->get()
         ->map(function ($request) {
            return [
               'id' => $request->id,
               'message' => $request->request_message,
               'created_at' => $request->created_at->toISOString(),
               'patient' => [
                  'id' => $request->patient->id,
                  'name' => $request->patient->name,
                  'email' => $request->patient->email,
                  'profile' => $request->patient->profile ? [
                     'photo_profile' => $request->patient->profile->photo_profile,
                  ] : null,
               ],
            ];
         });

      return response()->json(['requests' => $requests]);
   }

   /**
    * Accept patient request (doctor)
    */
   public function acceptRequest(Request $request, DoctorPatient $doctorPatient)
   {
      $user = Auth::user();

      if ($doctorPatient->doctor_id !== $user->id) {
         return response()->json(['error' => 'Unauthorized'], 403);
      }

      if ($doctorPatient->status !== 'pending') {
         return response()->json(['error' => 'Request sudah diproses'], 400);
      }

      $doctorPatient->accept($request->message);

      return response()->json([
         'success' => true,
         'message' => 'Pasien berhasil diterima',
      ]);
   }

   /**
    * Reject patient request (doctor)
    */
   public function rejectRequest(Request $request, DoctorPatient $doctorPatient)
   {
      $user = Auth::user();

      if ($doctorPatient->doctor_id !== $user->id) {
         return response()->json(['error' => 'Unauthorized'], 403);
      }

      if ($doctorPatient->status !== 'pending') {
         return response()->json(['error' => 'Request sudah diproses'], 400);
      }

      $doctorPatient->reject($request->message);

      return response()->json([
         'success' => true,
         'message' => 'Request pasien ditolak',
      ]);
   }

   /**
    * Get doctor's patients list
    */
   public function getMyPatients()
   {
      $user = Auth::user();

      if (!$user->isDoctor()) {
         return response()->json(['error' => 'Unauthorized'], 403);
      }

      $patients = DoctorPatient::where('doctor_id', $user->id)
         ->accepted()
         ->with([
            'patient:id,name,email',
            'patient.profile:user_id,photo_profile,gender,birth_date,height,weight',
            'alerts' => function ($query) {
               $query->latest()->take(5);
            }
         ])
         ->withCount([
            'alerts as unread_alerts_count' => function ($query) {
               $query->where('is_read', false);
            }
         ])
         ->get()
         ->map(function ($relation) {
            $profile = $relation->patient->profile;

            // Calculate age and BMI
            $age = null;
            $bmi = null;
            $bmiCategory = null;

            if ($profile && $profile->birth_date) {
               $age = \Carbon\Carbon::parse($profile->birth_date)->age;
            }

            if ($profile && $profile->height && $profile->weight) {
               $heightM = $profile->height / 100;
               $bmi = round($profile->weight / ($heightM * $heightM), 1);

               if ($bmi < 18.5)
                  $bmiCategory = 'Underweight';
               elseif ($bmi < 25)
                  $bmiCategory = 'Normal';
               elseif ($bmi < 30)
                  $bmiCategory = 'Overweight';
               else
                  $bmiCategory = 'Obese';
            }

            return [
               'id' => $relation->id,
               'doctor_id' => $relation->doctor_id,
               'patient_id' => $relation->patient_id,
               'status' => $relation->status,
               'created_at' => $relation->accepted_at?->toISOString() ?? $relation->created_at->toISOString(),
               'patient' => [
                  'id' => $relation->patient->id,
                  'name' => $relation->patient->name,
                  'email' => $relation->patient->email,
                  'profile' => $profile ? [
                     'photo_profile' => $profile->photo_profile,
                     'gender' => $profile->gender,
                     'age' => $age,
                     'height' => $profile->height,
                     'weight' => $profile->weight,
                     'bmi' => $bmi,
                     'bmi_category' => $bmiCategory,
                  ] : null,
               ],
               'alerts' => $relation->alerts->map(function ($alert) {
                  return [
                     'id' => $alert->id,
                     'alert_type' => $alert->alert_type,
                     'triggered_text' => $alert->triggered_text,
                     'matched_keywords' => $alert->matched_keywords,
                     'is_read' => $alert->is_read,
                     'created_at' => $alert->created_at->toISOString(),
                  ];
               }),
               'unread_alerts_count' => $relation->unread_alerts_count,
            ];
         });

      return response()->json(['patients' => $patients]);
   }

   /**
    * Get patient detail for doctor (health data)
    */
   public function getPatientDetail(DoctorPatient $doctorPatient)
   {
      $user = Auth::user();

      if ($doctorPatient->doctor_id !== $user->id) {
         return response()->json(['error' => 'Unauthorized'], 403);
      }

      if (!$doctorPatient->isActive()) {
         return response()->json(['error' => 'Relasi tidak aktif'], 400);
      }

      $patient = $doctorPatient->patient;
      $profile = $patient->profile;

      // Calculate age and BMI
      $age = null;
      $bmi = null;
      $bmiCategory = null;

      if ($profile && $profile->birth_date) {
         $age = \Carbon\Carbon::parse($profile->birth_date)->age;
      }

      if ($profile && $profile->height && $profile->weight) {
         $heightM = $profile->height / 100;
         $bmi = round($profile->weight / ($heightM * $heightM), 1);

         if ($bmi < 18.5)
            $bmiCategory = 'Underweight';
         elseif ($bmi < 25)
            $bmiCategory = 'Normal';
         elseif ($bmi < 30)
            $bmiCategory = 'Overweight';
         else
            $bmiCategory = 'Obese';
      }

      // Get health data
      $mentalLogs = $patient->mentalHealthLogs()
         ->orderBy('logged_at', 'desc')
         ->take(10)
         ->get()
         ->map(function ($log) {
            return [
               'id' => $log->id,
               'mood_level' => $log->mood_level,
               'notes' => $log->notes,
               'recorded_at' => $log->logged_at->toISOString(),
            ];
         });

      $physicalLogs = $patient->physicalHealthLogs()
         ->orderBy('logged_at', 'desc')
         ->take(10)
         ->get()
         ->map(function ($log) {
            return [
               'id' => $log->id,
               'activity_type' => $log->activity_type,
               'duration_minutes' => $log->duration_minutes,
               'recorded_at' => $log->logged_at->toISOString(),
            ];
         });

      // Get recent chat sessions
      $chatSessions = $patient->chatSessions()
         ->withCount('messages')
         ->latest()
         ->take(5)
         ->get()
         ->map(function ($session) {
            return [
               'id' => $session->id,
               'title' => $session->title,
               'messages_count' => $session->messages_count,
               'created_at' => $session->created_at->toISOString(),
            ];
         });

      // Get alerts
      $alerts = $doctorPatient->alerts()
         ->latest()
         ->take(20)
         ->get()
         ->map(function ($alert) {
            return [
               'id' => $alert->id,
               'alert_type' => $alert->alert_type,
               'triggered_text' => $alert->triggered_text,
               'matched_keywords' => $alert->matched_keywords,
               'is_read' => $alert->is_read,
               'created_at' => $alert->created_at->toISOString(),
            ];
         });

      return response()->json([
         'patient' => [
            'id' => $patient->id,
            'name' => $patient->name,
            'email' => $patient->email,
            'profile' => $profile ? [
               'photo_profile' => $profile->photo_profile,
               'gender' => $profile->gender,
               'age' => $age,
               'height' => $profile->height,
               'weight' => $profile->weight,
               'bmi' => $bmi,
               'bmi_category' => $bmiCategory,
            ] : null,
         ],
         'recent_mental_logs' => $mentalLogs,
         'recent_physical_logs' => $physicalLogs,
         'recent_sessions' => $chatSessions,
         'alerts' => $alerts,
      ]);
   }

   /**
    * Mark alerts as read
    */
   public function markAlertsRead(DoctorPatient $doctorPatient)
   {
      $user = Auth::user();

      if ($doctorPatient->doctor_id !== $user->id) {
         return response()->json(['error' => 'Unauthorized'], 403);
      }

      $doctorPatient->alerts()->unread()->update([
         'is_read' => true,
         'read_at' => now(),
      ]);

      return response()->json(['success' => true]);
   }

   /**
    * Remove patient (doctor)
    */
   public function removePatient(DoctorPatient $doctorPatient)
   {
      $user = Auth::user();

      if ($doctorPatient->doctor_id !== $user->id) {
         return response()->json(['error' => 'Unauthorized'], 403);
      }

      $doctorPatient->delete();

      return response()->json([
         'success' => true,
         'message' => 'Pasien berhasil dihapus dari daftar',
      ]);
   }
}
