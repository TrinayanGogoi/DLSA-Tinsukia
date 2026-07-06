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
        Schema::create('latest', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('uploads_id')->index(); // Foreign key column (indexed)
            $table->date('expires_at')->nullable(); // Date when the latest will expire
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('uploads_id')->references('id')->on('uploads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('latest');
    }
};
