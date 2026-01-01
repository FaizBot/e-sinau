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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();

            $table->string('title', 150);
            $table->string('slug', 180)->unique();

            $table->text('description')->nullable();

            $table->longText('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('video_url')->nullable();

            $table->enum('type', ['text', 'file', 'video'])->default('text');

            $table->boolean('is_published')->default(true);
            $table->integer('order_number')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
