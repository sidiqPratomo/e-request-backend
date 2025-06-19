<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePrivileges extends Model
{
    use HasFactory;

    public const CREATED_AT = 'created_time';

    public const UPDATED_AT = 'updated_time';

    protected $fillable = [
        'id',
        'role',
        'action',
        'uri',
        'method',
    ];

    public function getRole()
    {
        return $this->hasMany(Roles::class, 'id');
    }

    public function getPrivilege()
    {
        return $this->hasMany(Privileges::class, 'id');
    }
}
