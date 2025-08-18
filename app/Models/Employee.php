<?php

namespace App\Models;

use App\Models\EmployeeTraining;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'designation_id',
        'mobile',
        'email',
        'working_place',
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    // Relationship with EmployeeTraining pivot model
    public function trainings() {
        return $this->belongsToMany(Training::class, 'employee_training')
               ->using(EmployeeTraining::class)
               ->withTimestamps();
    }
}
