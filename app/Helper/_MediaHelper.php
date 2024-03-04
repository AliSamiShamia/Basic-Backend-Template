<?php

namespace App\Helper;

use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Gravity;
use Cloudinary\Transformation\Resize;
use Cloudinary\Transformation\Transcode;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class _MediaHelper
{

    private $cloudinary;

    /**
     */
    public function __construct()
    {
//        $this->cloudinary = new Cloudinary([
//            'cloud' => [
//                
//            ],
//
//        ]);
    }


    public function upload($file, $public_ID)
    {

        $path = public_path('uploads/images');
        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
        Image::make($file->getRealPath())->save($path . '/' . $public_ID);
        return true;
//        try {
//            $res = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
//                "public_id" => $public_ID,
//                "unique_filename" => true,
//                "async" => true
//            ]);
//            Log::error(json_encode($res));
//            return true;
//        } catch (ApiError $e) {
//            Log::error($e);
//            return null;
//        }
    }

    public function uploadVideo($file, $public_ID)
    {
//        try {
//            $res = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
//                'resource_type' => 'video',
//                "public_id" => $public_ID,
//                "unique_filename" => true,
//                "async" => true
//            ]);
//            return true;
//        } catch (ApiError $e) {
//            Log::error($e);
//            return null;
//        }
    }

    public static function getURL($publicID, $type, $width = null): string
    {

//        $helper = new _MediaHelper();
//        if ($type != "video" && $type != "video2" && $type != "promo") {
//            $url = $helper->cloudinary->image($publicID);
//            if ($width) {
//                $url = $url->resize(Resize::fill()->width($width));
//            }
//
//            $url = $url->signUrl();
//        } else {
//            $url = $helper->cloudinary->video($publicID)
////                ->transcode(Transcode::bitRate(1000000))
//                ->signUrl();
//        }
//        Log::error($publicID);
        return asset('uploads/images/' . $publicID);
//        return "";
    }

    public function delete($publicID, $type)
    {
        $path = asset('uploads/images/' . $publicID);
        if (File::exists($path)) {
            // Delete the file
            File::delete($path);
            return true;
        }
//        try {
//            return $this->cloudinary->uploadApi()->destroy($url, ['resource_type' => $type]);
//        } catch (Exception $exception) {
//            Log::error($exception);
//            return null;
//        }
        return null;
    }


}
