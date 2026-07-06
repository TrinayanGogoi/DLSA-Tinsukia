<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uploads extends Model
{
    use HasFactory;

    // Define table name (optional if it follows naming conventions)
    protected $table = 'uploads';

    // Define fillable columns to allow mass assignment
    protected $fillable = [
        'title',
        'description',
        'upload_date',
        'event_date',
        'location',
    ];

    // Disable timestamps since the table doesn't have created_at and updated_at
    public $timestamps = false;

    /**
     * Define the relationship with the Tags model.
     */
    public function tags()
        {
            return $this->hasMany(Tags::class, 'uploads_id'); // Tags is the model name
        }
    /**
     * Define the relationship with the Links model.
     *
     * This method establishes a one-to-many relationship between the Uploads model and the Links model.
     * Each upload has multiple links associated with it, identified by the 'uploads_id' foreign key in the 'links' table.
     *
     */
    public function links()
        {
            return $this->hasMany(Links::class, 'uploads_id');
        }
    /**
     * Define the relationship with the Uploads_has_categories model.
     */
    public function pictures()
        {
            return $this->hasMany(Pictures::class, 'uploads_id');
        }

    public function pdfs()
    {
        return $this->hasMany(Pdfs::class, 'uploads_id');
    }
}
