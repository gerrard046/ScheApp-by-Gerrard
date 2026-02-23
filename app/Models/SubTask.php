<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTask extends Model
{
    protected $fillable = ['schedule_id', 'title', 'is_completed'];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
