<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Mediable;
use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, LogsActivity, Mediable,UserTrait;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'country',
        'country_code',
        'role',
        'phone_number',
        'password',
        'has_permission'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if (!$model->phone_number) {
                $model->phone_number = $model->id;
            }
        });
        self::created(function ($model) {
            if (!$model->email) {
                $model->email = $model->id . '@jwpharma.com';
            }
            if (!$model->phone_number) {
                $model->phone_number = $model->id;
            }
        });
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['phone_number'])
            ->setDescriptionForEvent(fn(string $eventName) => "User {$eventName}")
            ->useLogName('user log')
            ->dontLogIfAttributesChangedOnly(['country', 'middle_name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();


        // Chain fluent methods for configuration options
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }


}
