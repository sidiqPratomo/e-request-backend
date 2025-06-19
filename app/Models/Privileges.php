<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class Privileges extends Resources
{
    use HasFactory;

    protected $table = 'privileges';

    protected $fillable = [
        'module',
        'submodule',
        'ordering',
        'action',
        'method',
        'uri',
    ];

    protected $forms = [
        [
            'name' => 'module',
            'required' => true,
            'column' => 4,
            'label' => 'Module',
            'type' => 'text',
            'display' => true,
        ],
        [
            'name' => 'submodule',
            'required' => true,
            'column' => 4,
            'label' => 'Sub Modul',
            'type' => 'text',
            'display' => true,
        ],
        [
            'name' => 'ordering',
            'required' => true,
            'column' => 4,
            'label' => 'Ordering',
            'type' => 'text',
            'display' => true,
        ],
        [
            'name' => 'action',
            'required' => true,
            'column' => 4,
            'label' => 'Action',
            'type' => 'text',
            'display' => true,
        ],
        [
            'name' => 'method',
            'required' => true,
            'column' => 4,
            'label' => 'Method',
            'type' => 'text',
            'display' => true,
        ],
        [
            'name' => 'uri',
            'required' => true,
            'column' => 4,
            'label' => 'URI',
            'type' => 'text',
            'display' => true,
        ],
    ];

    protected $hidden = [];

    protected $rules = [
        'module' => ['required', 'string'],
        'submodule' => ['required', 'string'],
        'ordering' => ['required', 'string'],
        'action' => ['required', 'string'],
        'method' => ['required', 'string'],
        'uri' => ['required', 'string'],
    ];

    protected $searchable = [
        'module',
        'submodule',
    ];

    public function getRules()
    {
        return $this->rules;
    }

    public function getFields()
    {
        return $this->fillable;
    }

    public function getForms()
    {
        return $this->forms;
    }

    public function checkTableExists($table_name)
    {
        return Schema::hasTable($table_name);
    }

    public function getTableFields()
    {
        return Schema::getColumnListing($this->getTable());
    }

    public function getUser()
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }

    public function getRole()
    {
        return $this->hasMany(Roles::class, 'role_id', 'id');
    }

    public function getPrivilege()
    {
        return $this->belongsTo(Privileges::class, 'id');
    }

    public function permission()
    {
        return $this->hasMany(RolePrivileges::class, 'id');
    }
}
