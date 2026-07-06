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
        Schema::create('slider_picture', function (Blueprint $table) {
            $table->id();
            $table->text('slider_picture_path')->nullable(); // PATH of the picture file saved in the server
            $table->string('slider_picture_title')->nullable(); // Picture title/caption (Optional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slider_picture');
    }
};
