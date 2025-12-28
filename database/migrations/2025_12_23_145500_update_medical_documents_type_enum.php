<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MariaDB/MySQL, we need to use raw SQL to modify ENUM
        DB::statement("ALTER TABLE `medical_documents` MODIFY COLUMN `type` ENUM('disease', 'symptom', 'drug', 'procedure', 'guideline', 'research', 'other') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `medical_documents` MODIFY COLUMN `type` ENUM('disease', 'symptom', 'drug') NOT NULL");
    }
};
