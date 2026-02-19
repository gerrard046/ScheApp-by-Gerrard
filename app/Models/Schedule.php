<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    // Tambahkan ini agar Laravel mengizinkan kolom-kolom ini diisi
    protected $fillable = ['activity_name', 'category', 'date', 'time', 'group_name'];
}