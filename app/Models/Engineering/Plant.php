<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plant extends Model
{
    use HasFactory;

    protected $table = 'plants';

    protected $fillable = [
        'name',
        'code'
    ];

    /**
     * Get the machines for the plant.
     */
    public function machines(): HasMany
    {
        return $this->hasMany(Machine::class, 'plant_id');
    }
}
