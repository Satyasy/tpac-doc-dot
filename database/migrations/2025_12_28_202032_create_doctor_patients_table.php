<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctor_patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('request_message')->nullable(); // Message from patient when requesting
            $table->text('response_message')->nullable(); // Response from doctor
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            // Ensure unique doctor-patient pair
            $table->unique(['doctor_id', 'patient_id']);
        });

        // Table for forbidden word alerts
        Schema::create('patient_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_patient_id')->constrained('doctor_patients')->onDelete('cascade');
            $table->foreignId('chat_message_id')->nullable()->constrained('chat_messages')->onDelete('set null');
            $table->string('alert_type')->default('forbidden_word'); // forbidden_word, low_mood, etc.
            $table->text('triggered_text')->nullable(); // The text that triggered the alert
            $table->text('matched_keywords')->nullable(); // JSON array of matched keywords
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_alerts');
        Schema::dropIfExists('doctor_patients');
    }
};
