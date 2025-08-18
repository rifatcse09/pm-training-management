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
        'assigned_at',
        'assigned_by',
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

    // Relationship with User model for assigned_by
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Accessor for meta field
    public function getMetaAttribute($value)
    {
        return json_decode($value, true);
    }

}
