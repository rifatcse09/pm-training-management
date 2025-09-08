<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $table = 'designations';

    protected $fillable = ['grade', 'name', 'class'];

    public function users()
    {
        return $this->hasMany(User::class, 'designation_id');
    }

    public function employeeTrainings()
    {
        return $this->hasMany(EmployeeTraining::class);
    }

}
