<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Discount extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        "name",
        "description",
        "discount_percent",
        "active",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'discount_percent', 'active'])
            ->setDescriptionForEvent(fn (string $eventName) => "Discount {$eventName}")
            ->useLogName('dicount log')
            ->dontLogIfAttributesChangedOnly(['brief', 'weight'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function orderItem(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'dicount_id');
    }
}
