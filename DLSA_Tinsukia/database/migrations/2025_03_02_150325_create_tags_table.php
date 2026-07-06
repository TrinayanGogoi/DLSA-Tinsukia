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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('uploads_id'); // Foreign key column
            $table->boolean('achievement')->default(false); // Boolean column with default false (true/false)
            $table->boolean('activity_calendar')->default(false);
            $table->boolean('advertisement')->default(false);
            $table->boolean('awareness_meeting')->default(false);
            $table->boolean('awareness_program')->default(false);
            $table->boolean('juvenile_justice')->default(false);
            $table->boolean('legal_aid')->default(false);
            $table->boolean('legal_assistance')->default(false);
            $table->boolean('lok_adalat')->default(false);
            $table->boolean('legal_literacy_classes')->default(false);
            $table->boolean('mediation')->default(false);
            $table->boolean('monitoring_legal_clinic')->default(false);
            $table->boolean('monitoring_jail')->default(false);
            $table->boolean('meeting')->default(false);
            $table->boolean('notice')->default(false);
            $table->boolean('observance')->default(false);
            $table->boolean('results')->default(false);
            $table->boolean('schemes')->default(false);
            $table->boolean('victim_compensation')->default(false);
            $table->boolean('workshop')->default(false);
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
        Schema::dropIfExists('tags');
    }
};
