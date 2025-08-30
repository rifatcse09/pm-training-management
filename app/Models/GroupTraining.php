<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupTraining extends Model
{
    protected $table = 'group_training';

    protected $fillable = [
        'start_date',
        'end_date',
        'total_days',
        'file_link',
        'file_name',
    ];

    // Relationship with EmployeeTraining
    public function employeeTrainings()
    {
        return $this->hasMany(EmployeeTraining::class, 'group_training_id');
    }

    // Accessor for file_link
    public function getFileLinkAttribute()
    {
        return $this->file_name ? asset('storage/' . $this->file_name) : null;
    }
}
