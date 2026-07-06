<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Slider_Picture extends Model
{
    use HasFactory;

    // Define table name (optional if it follows naming conventions)
    protected $table = 'slider_picture';

    // Define fillable columns to allow mass assignment
    protected $fillable = [
        'slider_picture_path',
        'slider_picture_title',
    ];
}
