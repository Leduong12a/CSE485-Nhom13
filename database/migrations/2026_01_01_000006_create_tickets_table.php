<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('location', 150)->nullable();
            $table->foreignId('category_id')->constrained('ticket_categories')->restrictOnDelete();
            $table->foreignId('requester_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('current_assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED', 'REOPENED'])->default('OPEN');
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH'])->default('MEDIUM');
            $table->timestamp('sla_deadline')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('sla_deadline');
            $table->index('requester_id');
            $table->index('current_assignee_id');
            $table->index(['current_assignee_id', 'priority', 'sla_deadline'], 'idx_tickets_assignee_priority_sla');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
