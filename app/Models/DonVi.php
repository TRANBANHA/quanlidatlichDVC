<?php
// Model Category
namespace App\Models;

use App\Models\SoNha;
use App\Models\Phuong;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonVi extends Model
{
    use HasFactory;
    protected $table = 'don_vi';
    protected $fillable = [
        'ten_don_vi',
        'mo_ta',
        'qr_bank_name',
        'qr_account_number',
        'qr_account_name',
        'qr_image',
    ];

    public function admins()
    {
        return $this->hasMany(Admin::class, 'don_vi_id');
    }
}
