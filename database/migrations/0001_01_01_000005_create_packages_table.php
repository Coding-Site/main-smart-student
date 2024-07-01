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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->required();
            $table->string('name_en')->nullable();
            $table->enum('period_type', ['باقة ترم', 'باقة شهرية'])->nullable();
            $table->enum('type', ['ذهبية', 'فضية','ماسية'])->nullable();
            $table->string('description')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('price_gold', 10, 2)->nullable()->default(0.0);
            $table->decimal('price_silver', 10, 2)->nullable()->default(0.0);
            $table->decimal('discount_percentage', 5, 2)->default(50.0)->max(100.0);
            $table->decimal('teacher_percentage', 5, 2)->default(0.0)->max(100.0);
            $table->decimal('price', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('classroom_id')->nullable();
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
