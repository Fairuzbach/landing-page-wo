<?php

namespace App\Models\GeneralAffair;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\GeneralAffair\WorkOrderGaHistory;

class WorkOrderGeneralAffair extends Model
{
    use HasFactory;

    protected $table = 'work_order_general_affairs';

    protected $fillable = [
        'ticket_num',
        'requester_id',
        'requester_name',
        'plant',
        'department',
        'description',
        'category',
        'parameter_permintaan',
        'status',
        'status_permintaan',
        'target_completion_date',
        'actual_completion_date',
        'photo_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
    public function processor()
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by');
    }

    public function histories()
    {
        return $this->hasMany(WorkOrderGaHistory::class, 'work_order_id')->latest();
    }
}
