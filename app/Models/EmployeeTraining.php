<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTraining extends Pivot
{
    use SoftDeletes;

    protected $table = 'employee_training';

    protected $fillable = [
        'employee_id',
        'training_id',
        'designation_id', // Add designation_id to fillable
        'assigned_at',
        'assigned_by',
        'working_place', // Add working_place to fillable
    ];

    protected $casts = [
        'assigned_at' => 'date',
    ];

    // Relationship with Employee model
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Relationship with Training model
    public function training()
    {
        return $this->belongsTo(Training::class);
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
