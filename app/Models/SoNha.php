<?php
// Model Category
namespace App\Models;

use App\Models\Duong;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoNha extends Model
{
    use HasFactory;
    protected $table = 'table_so_nha';
    protected $fillable = [
        'so_nha',
        'mo_ta',
        'duong_id'
    ];

    public function duong()
    {
        return $this->belongsTo(Duong::class, 'duong_id');
    }
}
