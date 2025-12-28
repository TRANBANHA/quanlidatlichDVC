<?php
// Model Category
namespace App\Models;

use App\Models\SoNha;
use App\Models\Phuong;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Duong extends Model
{
    use HasFactory;
    protected $table = 'duong';
    protected $fillable = [
        'ten_duong',
        'mo_ta',
        'phuong_id'
    ];
    public function phuong()
    {
        return $this->belongsTo(Phuong::class, 'phuong_id');
    }

    public function soNhas()
    {
        return $this->hasMany(SoNha::class, 'duong_id');
    }
}
