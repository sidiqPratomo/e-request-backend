<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Resources
{
    use HasFactory;

    protected $table = 'province';

    protected $fillable = ['name'];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'id_provinsi');
    }
}
