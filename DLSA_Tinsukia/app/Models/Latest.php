<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Latest extends Model
{
    use HasFactory;

    protected $table = 'latest';

        protected $fillable = [
        'uploads_id',
        'expires_at',
    ];

    /**
     * Define the relationship with the Uploads model.
     */
    public function upload()
    {
        return $this->belongsTo(Uploads::class, 'uploads_id');
    }
}


