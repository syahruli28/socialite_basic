<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        // many to one ke user
        return $this->belongsTo(User::class);
    }
}
