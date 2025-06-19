<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Resources
{
    use HasFactory;

    protected $table = 'city';

    protected $fillable = ['name', 'id_provinsi'];

    protected $searchable = ['name', 'id_provinsi'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'id_provinsi');
    }
}
