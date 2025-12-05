<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParameterImprovement extends Model
{
    use HasFactory;

    protected $table = 'improvement_parameters';
    protected $fillable = ['code', 'name'];
}
