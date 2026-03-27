<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'description', 'category'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
