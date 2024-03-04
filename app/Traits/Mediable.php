<?php


namespace App\Traits;


use App\Models\Media;

trait Mediable
{
    public function allMedia()
    {
        return $this->morphMany(Media::class, 'mediable')->orderByDesc('priority');
    }

    public function images()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'image')->orderByDesc('priority');
    }

    public function profile()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'profile')->orderByDesc('priority');
    }

    public function banners()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'banner')->orderByDesc('priority');
    }

    public function galleries()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'gallery')->orderByDesc('priority');
    }

    public function cover()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'cover')->orderByDesc('priority');
    }


    public function videos()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'video')->orderByDesc('priority');
    }

}
