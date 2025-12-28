<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'cai_dat';

    protected $fillable = ['khoa', 'gia_tri']; // Các trường có thể được gán hàng loạt
}