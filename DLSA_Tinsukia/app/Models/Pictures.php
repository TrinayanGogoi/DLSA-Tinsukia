<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pictures extends Model
{
    use HasFactory;

    protected $table = 'pictures';

    protected $fillable = [
        'uploads_id',
        'picture_path',
        'picture_title',
    ];

    /**
     * Define the relationship with the Uploads model.
     */
    public function upload()
    {
        return $this->belongsTo(Uploads::class, 'uploads_id');
    }
}
