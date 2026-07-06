<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Links extends Model
{
    use HasFactory;

    protected $table = 'links';

    protected $fillable = [
        'uploads_id',
        'link_title',
        'link_url',
        'link_location',
    ];

    /**
     * Define the relationship with the Uploads model.
     */
    public function uploads()
    {
        return $this->belongsTo(Uploads::class, 'uploads_id');
    }
}
