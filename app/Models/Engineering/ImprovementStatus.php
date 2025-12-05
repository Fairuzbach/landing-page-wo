<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImprovementStatus extends Model
{
    use HasFactory;

    protected $table = 'improvement_statuses';
    protected $fillable = ['name', 'slug', 'color'];
}
