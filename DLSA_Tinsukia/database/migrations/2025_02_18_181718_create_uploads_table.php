<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Title of the upload
            $table->text('description')->nullable(); // Description of the upload
            $table->date('upload_date'); // Date when the upload was made
            $table->date('event_date')->nullable(); // Date when the event will happen
            $table->string('location')->nullable(); // Location related to the upload
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
