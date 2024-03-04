<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOtp extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'otp',
        'email',
        'phone_number',
        'expired_at',
        'user_id',
        'status',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
