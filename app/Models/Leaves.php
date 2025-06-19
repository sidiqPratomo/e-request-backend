<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaves extends Resources
{
    use HasFactory;

    protected $table = 'leaves';

    protected $fillable = [
        'user_id', 'employee_id', 'files', 'leave_date', 'return_date'
    ];

    protected $reference = [];

    protected $searchable = ['name', 'nik'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id', 'id');
    }
}
