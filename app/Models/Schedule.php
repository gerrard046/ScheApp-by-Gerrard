<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    // Tambahkan ini: Daftar field yang boleh diisi
    protected $fillable = [
        'user_name',
        'group_name',
        'activity_name',
        'category',
        'date',
        'time',
    ];
}