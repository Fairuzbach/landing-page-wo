<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class WorkOrderEngineering extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai dengan database Anda
    protected $table = 'work_order_engineerings';

    // --- BAGIAN PENTING: DAFTARKAN SEMUA KOLOM BARU DI SINI ---
    // Jika kolom tidak ada di sini, update() di controller akan mengabaikannya (silent fail)
    protected $fillable = [
        'requester_id',
        'ticket_num',
        'report_date',
        'report_time',
        'plant',
        'machine_name',
        'damaged_part',

        'improvement_parameters', // Kolom Baru
        'improvement_status',    // Kolom Baru (PENTING untuk status)

        'kerusakan',
        'kerusakan_detail',
        'priority',
        // 'work_status',           // Simpan untuk backward compatibility (opsional)
        'photo_path',
        'finished_date',
        'start_time',
        'end_time',

        'engineer_tech',         // Kolom Baru (PENTING untuk teknisi)

        'technician',
        'maintenance_note',
        'repair_solution',
        'sparepart',
    ];

    // Relasi ke User (Pelapor)
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
}
