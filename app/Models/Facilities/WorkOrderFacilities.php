<?php

namespace App\Models\Facilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Testing\Fluent\Concerns\Has;
use App\Models\FacilityTech;
use App\Models\Engineering\Machine;

class WorkOrderFacilities extends Model
{
    use HasFactory;

    protected $table = 'work_order_facilities';
    protected $guarded = ['id']; // Membuka semua kolom agar bisa diisi

    // 1. Relasi ke User (Requester)
    public function user()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    // 2. [INI YANG HILANG] Relasi ke Teknisi
    public function facilityTech()
    {
        // Parameter ke-2 ('facility_tech_id') harus sesuai nama kolom di database
        return $this->belongsTo(FacilityTech::class, 'facility_tech_id');
    }

    // 3. Relasi ke Mesin
    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }
}
