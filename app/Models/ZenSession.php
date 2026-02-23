<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZenSession extends Model
{
    protected $fillable = ['user_id', 'duration_minutes', 'date'];
}
