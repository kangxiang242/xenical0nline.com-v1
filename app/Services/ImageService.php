<?php


namespace App\Services;

use \Image;
class ImageService
{

    public function resize($path,$size){
        if(is_file($path)){
            try {
                $img = Image::make($path)->resize($size, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $saveName = $img->filename.'-'.$size.'.'.$img->extension;
                $img->save($img->dirname.'/'.$saveName);
            }catch (\Exception $exception){

            }

        }
    }

}
