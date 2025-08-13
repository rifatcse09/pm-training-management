<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'organization_id',
        'start_date',
        'end_date',
        'total_days',
    ];

    /**
     * Relationship with the Organizer model.
     */
    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organization_id');
    }
}
