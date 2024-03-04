<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GlobalController extends Controller
{

    public function countries(): bool|string
    {
        $path = public_path() . "/country.json";
        return file_get_contents($path);
    }

    /***
     * this function generates the website's sitemap, providing a response containing
     * arrays of all products, collections,
     * and their respective links. This comprehensive information proves
     * highly beneficial for SEO purposes.
     ***/
    public function sitemap()
    {
        return [];
    }
}
