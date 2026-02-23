<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupResource extends Model
{
    protected $fillable = ['group_id', 'user_id', 'title', 'file_path', 'file_type'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
