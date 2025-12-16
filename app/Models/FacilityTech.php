<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityTech extends Model
{
    use HasFactory;

    // Sesuaikan nama tabel jika perlu (Laravel biasanya otomatis baca 'facility_teches' atau 'facility_techs')
    protected $table = 'facility_teches';

    protected $guarded = ['id'];
}
