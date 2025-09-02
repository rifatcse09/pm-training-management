<?php

namespace App\Models;

use App\Models\EmployeeTraining;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\WorkingPlaceEnum;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'designation_id',
        'mobile',
        'email',
        'working_place', // Ensure this column exists in the database
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function employeeTrainings() {
        return $this->hasMany(EmployeeTraining::class);
    }

    public function trainings()
    {
        return $this->belongsToMany(GroupTraining::class, 'employee_training')
            ->using(EmployeeTraining::class)
            ->withTimestamps();
    }

    public function getWorkingPlaceNameAttribute(): ?string
    {
        return WorkingPlaceEnum::getNameById($this->working_place);
    }
}
