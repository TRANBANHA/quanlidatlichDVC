<?php
// Model Category
namespace App\Models;

use App\Models\Duong;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Phuong extends Model
{
    use HasFactory;
    protected $table = 'phuong';
    protected $fillable = [
        'ten_phuong',
        'mo_ta'
    ];

    public function duongs()
    {
        return $this->hasMany(Duong::class, 'phuong_id');
    }
}
