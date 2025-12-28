<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceScheduleStaff extends Model
{
    use HasFactory;

    protected $table = 'service_schedule_staff';

    protected $fillable = [
        'schedule_id',
        'can_bo_id',
    ];

    /**
     * Quan hệ: Phân công thuộc về 1 lịch dịch vụ
     */
    public function schedule()
    {
        return $this->belongsTo(ServiceSchedule::class, 'schedule_id');
    }

    /**
     * Quan hệ: Phân công thuộc về 1 cán bộ
     */
    public function canBo()
    {
        return $this->belongsTo(Admin::class, 'can_bo_id');
    }
}
