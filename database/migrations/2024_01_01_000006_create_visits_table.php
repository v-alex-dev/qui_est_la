<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('visitors')->onDelete('cascade');
            $table->foreignId('staff_member_id')->nullable()->constrained('staff_members')->onDelete('set null');
            $table->foreignId('training_id')->nullable()->constrained('trainings')->onDelete('set null');
            $table->enum('purpose', ['visite', 'formation']);
            $table->timestamp('entered_at');
            $table->timestamp('exited_at')->nullable();
            $table->string('badge_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
}; 