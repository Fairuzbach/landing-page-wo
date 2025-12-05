<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineerTech extends Model
{
    use HasFactory;

    // Saya set nama tabelnya eksplisit agar tidak bingung pluralisasinya
    protected $table = 'engineer_teches';

    protected $fillable = [
        'name'
    ];
}
