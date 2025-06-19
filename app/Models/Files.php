<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;

    public const CREATED_AT = 'created_time';

    public const UPDATED_AT = 'updated_time';
}
