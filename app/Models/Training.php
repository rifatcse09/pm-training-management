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
    ];

     // Derived flag (from type)
     protected $appends = ['is_foreign'];

     public function getIsForeignAttribute(): bool
     {
         return (int)$this->type === 2;
     }

    // Relationship with the Organizer model
    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organization_id');
    }

    // Relationship with the Country model through the country_training pivot table
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_training')
                    ->withTimestamps();
    }


    // Relationship with EmployeeTraining pivot model
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_training')
                    ->using(EmployeeTraining::class)
                    ->withTimestamps();
    }

    public function employeeTrainings() {
        return $this->hasMany(EmployeeTraining::class);
    }
}
