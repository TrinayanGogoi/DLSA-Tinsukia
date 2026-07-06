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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('uploads_id')->index(); // Foreign key column
            $table->string('link_title')->nullable(); // Title of the LINK
            $table->text('link_url')->nullable(); // URL of the link
            $table->string('link_location')->nullable(); // Location related to the link
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
        Schema::dropIfExists('links');
    }
};
