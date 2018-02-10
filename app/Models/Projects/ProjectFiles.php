<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class ProjectFiles extends Model
{
    public static function getFileIcon($file,$size=''){
        $ext =strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $icon = self::mimes($ext);

        return "<i class=\"fa fa-{$icon} {$size}\"></i>";

    }

    static function mimes($ext){
        $mime_types =array(

            'txt' => 'file-text-o',
            'htm' => 'file-o',
            //'html' => 'text/html',
            'php' => 'file-code-o text-primary',
            //'css' => 'text/css',
            //js' => 'application/javascript',
            //'json' => 'application/json',
            //'xml' => 'application/xml',
//            'swf' => 'application/x-shockwave-flash',
//            'flv' => 'video/x-flv',

            // images
            'png' => 'file-image-o text-info',
            'jpe' => 'file-image-o text-info',
            'jpeg' => 'file-image-o text-info',
            'jpg' => 'file-image-o text-info',
            'gif' => 'file-image',
            'bmp' => 'file-image-o',
            'ico' => 'file-image-o',
            'tiff' => 'file-image-o',
            'tif' => 'file-image-o',
            'svg' => 'file-image-o',
            'svgz' => 'file-image-o',

            // archives
            'zip' => 'file-zip-o  text-warning',
            'rar' => 'file-zip-o  text-warning',
            'exe' => 'file-zip-o  text-warning',
            'msi' => 'file-zip-o  text-warning',
            'cab' => 'file-zip-o  text-warning',

            // audio/video
            'mp3' => 'file-audio-o',
            'qt' => 'file-audio-o',
            'mov' => 'file-video-o',

            // adobe
            'pdf' => 'file-pdf-o text-danger',
            'psd' => 'file-o  text-primary',
            'ai' => 'file-o  text-primary',
            'eps' => 'file-o  text-primary',
            'ps' => 'file-o  text-primary',

            // ms office
            'doc' => 'file-word-o  text-info',
            'rtf' => 'file-o  text-info',
            'xls' => 'file-excel-o  text-success',
            'ppt' => 'file-powerpoint-o  text-danger',

            // open office
            'odt' => 'file-o',
            'ods' => 'file-o'
        );

        if(array_key_exists ( $ext , $mime_types) ){
            return $mime_types[$ext];
        }else{
            return 'file-o';
        }

    }
}
