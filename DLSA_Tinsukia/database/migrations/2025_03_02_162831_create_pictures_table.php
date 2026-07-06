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
        Schema::create('pictures', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('uploads_id')->index(); // Foreign key column (indexed)
            $table->text('picture_path')->nullable(); // PATH of the picture file saved in the server
            $table->string('picture_title')->nullable(); // Picture title/caption (Optional)
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
        Schema::dropIfExists('pictures');
    }
};
