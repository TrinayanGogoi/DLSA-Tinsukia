<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tags extends Model
{
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = [
        'uploads_id',
        'achievement',
        'activity_calendar',
        'advertisement',
        'awareness_meeting',
        'awareness_program',
        'juvenile_justice',
        'legal_aid',
        'legal_assistance',
        'lok_adalat',
        'legal_literacy_classes',
        'mediation',
        'monitoring_legal_clinic',
        'monitoring_jail',
        'meeting',
        'notice',
        'observance',
        'results',
        'schemes',
        'victim_compensation',
        'workshop',
        'recruitment',
    ];

    /**
     * Define the relationship with the Upload model.
     */
    public function uploads()
    {
        return $this->belongsTo(Uploads::class, 'uploads_id'); // uploads is the model name
    }
}
