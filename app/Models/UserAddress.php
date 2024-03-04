<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UserAddress extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'country',
        'country_code',
        'city',
        'building',
        'flat_number',
        'user_id',
        'address',
        'map_url',
        'type',
        'is_default',
        'user_ip',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'country', 'city', 'building', 'flat_number', 'map_url', 'type', 'is_default', 'user_ip'])
            ->setDescriptionForEvent(fn(string $eventName) => "User Address {$eventName}")
            ->useLogName('user address log')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
