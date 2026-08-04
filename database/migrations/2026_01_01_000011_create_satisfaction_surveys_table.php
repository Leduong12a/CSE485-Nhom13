<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->unique()->constrained('tickets')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating_stars');
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Add check constraint for rating_stars
        DB::statement('ALTER TABLE `satisfaction_surveys` ADD CONSTRAINT `chk_rating_stars` CHECK (`rating_stars` >= 1 AND `rating_stars` <= 5)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satisfaction_surveys');
    }
};
