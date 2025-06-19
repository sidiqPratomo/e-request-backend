<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees extends Resources
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'user_id', 'departments', 'position'
    ];

    protected $reference = [];

    protected $searchable = ['departments', 'npositionik'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
