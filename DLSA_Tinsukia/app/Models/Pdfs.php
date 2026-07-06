<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pdfs extends Model
{
    use HasFactory;

    protected $table = 'pdfs';

    protected $fillable = [
        'uploads_id',
        'pdf_path',
        'pdf_title',
    ];

    /**
     * Define the relationship with the Uploads model.
     */
    public function upload()
    {
        return $this->belongsTo(Uploads::class, 'uploads_id');
    }
}
