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
        'file_link',
        'file_name',
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

    // Accessor for file_link
    public function getFileLinkAttribute()
    {
        return $this->file_name ? asset('storage/' . $this->file_name) : null;
    }

    // Relationship with EmployeeTraining pivot model
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_training')
                    ->using(EmployeeTraining::class)
                    ->withTimestamps();
    }
}
