<?php

namespace App\Models;

use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;

    protected $fillable = ['role_name', 'role_description'];

    public function logins()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}