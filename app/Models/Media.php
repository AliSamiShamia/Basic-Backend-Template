<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['mediable_id', 'mediable_type', 'type', 'priority', 'thumb_url', 'mime_type', 'url'];

    protected $table = "media";
}
