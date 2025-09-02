<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTraining extends Model
{
    use SoftDeletes;

    protected $table = 'employee_training';

    protected $fillable = [
        'employee_id',
        'training_id',
        'designation_id',
        'assigned_at',
        'assigned_by',
        'working_place',
        'group_training_id',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    // Relationship with Employee model
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Relationship with Training model
    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id');
    }

    // Relationship with GroupTraining model
    public function groupTraining()
    {
        return $this->belongsTo(GroupTraining::class, 'group_training_id');
    }

    // Relationship with Designation model
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    // Relationship with User model for assigned_by
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
