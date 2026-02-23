<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    // Tambahkan ini agar Laravel mengizinkan kolom-kolom ini diisi
    protected $fillable = [
        'user_id',
        'group_id',
        'user_name',
        'activity_name',
        'category',
        'date',
        'time',
        'group_name',
        'priority',
        'is_completed',
        'is_verified',
        'proof_image',
        'attachment_file',
        'attachment_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subTasks()
    {
        return $this->hasMany(SubTask::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}