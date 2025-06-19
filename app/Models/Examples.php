<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Examples extends Resources
{
    use HasFactory;

    protected $guard_name = 'web';

    protected $table = 'examples';

    protected $rules = [
        'name' => [
            'create' => 'required', 'string', 'max:50',
            'update' => 'required', 'string', 'max:50',
            'delete' => 'nullable',
        ],
        'nik' => [
            'create' => 'required', 'string',
            'update' => 'required', 'string',
            'delete' => 'nullable',
        ],
        'dob' => [
            'create' => 'required', 'string',
            'update' => 'required', 'string',
            'delete' => 'nullable',
        ],
        'citizen' => [
            'create' => 'required', 'string',
            'update' => 'required', 'string',
            'delete' => 'nullable',
        ],
    ];

    protected $fillable = [
        'name', 'nik', 'dob', 'citizen',
        'hobbies', 'married_status', 'profile_picture',
        'supporting_document', 'age', 'taxpayer_number',
        'phone',
    ];

    protected $reference = [];

    protected $searchable = ['name', 'nik'];
}
