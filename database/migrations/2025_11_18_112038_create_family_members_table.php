<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->enum('gender', ['male','female','other'])->nullable();

            // Relationships
            $table->unsignedBigInteger('father_id')->nullable();
            $table->unsignedBigInteger('mother_id')->nullable();
            $table->unsignedBigInteger('spouse_id')->nullable();

            // Additional fields
            $table->string('occupation', 150)->nullable();
            $table->string('gotra', 150)->nullable();
            $table->string('mul', 150)->nullable();
            $table->date('dob')->nullable();
            $table->date('dod')->nullable();

            // citizenship FK
            $table->unsignedBigInteger('citizenship_id')->nullable();

            // photo
            $table->string('photo')->nullable();

            $table->timestamps();

            // Short-named indexes (avoid long auto-generated index name)
            $table->index('father_id', 'fm_father_idx');
            $table->index('mother_id', 'fm_mother_idx');
            $table->index('spouse_id', 'fm_spouse_idx');
            $table->index('citizenship_id', 'fm_citizenship_idx');

            // Foreign key constraints (nullable; nullOnDelete)
            $table->foreign('father_id')->references('id')->on('family_members')->nullOnDelete();
            $table->foreign('mother_id')->references('id')->on('family_members')->nullOnDelete();
            $table->foreign('spouse_id')->references('id')->on('family_members')->nullOnDelete();
            $table->foreign('citizenship_id')->references('id')->on('citizenships')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
