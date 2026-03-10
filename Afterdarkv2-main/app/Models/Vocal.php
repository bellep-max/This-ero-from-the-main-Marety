<?php

namespace App\Models;

use App\Enums\VocalGenderEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vocal extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    protected $casts = [
        'name' => VocalGenderEnum::class,
        'code' => VocalGenderEnum::class,
    ];

    public $timestamps = false;

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }
}
