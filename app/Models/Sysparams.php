<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class Sysparams extends Resources
{
    use HasFactory;

    protected $table = 'sysparams';

    protected $rules = [
        'group' => ['required', 'string'],
        'key' => ['required', 'string'],
        'value' => ['required', 'string'],
        'long_value' => ['required', 'string'],
    ];

    protected $fillable = [
        'group',
        'key',
        'value',
        'long_value',
    ];

    protected $searchable = [
        'group',
        'key',
        'value',
        'long_value',
    ];

    protected $hidden = [];

    protected $forms = [
        [
            'name' => 'group',
            'required' => true,
            'column' => 4,
            'label' => 'Group',
            'type' => 'text',
            'display' => true,
        ],
        [
            'name' => 'key',
            'required' => true,
            'column' => 4,
            'label' => 'Key',
            'type' => 'text',
            'display' => true,
        ],
        [
            'name' => 'value',
            'required' => true,
            'column' => 4,
            'label' => 'Value',
            'type' => 'text',
            'display' => true,
        ],
        [
            'name' => 'long_value',
            'required' => true,
            'column' => 4,
            'label' => 'Long Value',
            'type' => 'text',
            'display' => true,
        ],
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
}
