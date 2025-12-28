<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Service extends Authenticatable
{
    use HasFactory;
    protected $table = "dich_vu";
    protected $fillable = [
        'ten_dich_vu',
        'mo_ta',
    ];
    public function serviceSchedule()
    {
        return $this->hasOne(ServiceSchedule::class, 'dich_vu_id','id');
    }

    public function serviceFields()
    {
        return $this->hasMany(ServiceField::class, 'dich_vu_id')->orderBy('thu_tu');
    }

    public function servicePhuongs()
    {
        return $this->hasMany(ServicePhuong::class, 'dich_vu_id');
    }

    public function getServiceForPhuong($donViId)
    {
        return $this->servicePhuongs()->where('don_vi_id', $donViId)->first();
    }
}
