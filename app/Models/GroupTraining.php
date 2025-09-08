<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupTraining extends Model
{

    protected $fillable = [
        'start_date',
        'end_date',
        'total_days',
        'file_link',
        'file_name',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    // Relationship with EmployeeTraining
    public function employeeTrainings()
    {
        return $this->hasMany(EmployeeTraining::class, 'group_training_id');
    }

    // Relationship with Employees through EmployeeTraining
    public function employees()
    {
        return $this->hasManyThrough(
            Employee::class,
            EmployeeTraining::class,
            'group_training_id', // Foreign key on EmployeeTraining table
            'id', // Foreign key on Employee table
            'id', // Local key on GroupTraining table
            'employee_id' // Local key on EmployeeTraining table
        );
    }

    // Relationship with Organizer through EmployeeTraining and Training
    public function organizer()
    {
        return $this->hasOneThrough(
            Organizer::class,
            Training::class,
            'id', // Foreign key on Training table (training_id in EmployeeTraining)
            'id', // Foreign key on Organizer table
            'id', // Local key on GroupTraining table
            'organization_id' // Local key on Training table
        );
    }

    // Relationship with Countries through the country_training pivot table
    public function countries()
    {
        return $this->hasManyThrough(
            Country::class,
            Training::class,
            'id', // Foreign key on Training table (training_id in EmployeeTraining)
            'id', // Foreign key on Country table
            'id', // Local key on GroupTraining table
            'id' // Local key on Country table (via pivot table)
        );
    }

    // Accessor for file_link
    public function getFileLinkAttribute()
    {
        return $this->file_name ? asset('storage/group_training_files/' . $this->file_name) : null;
    }
}
