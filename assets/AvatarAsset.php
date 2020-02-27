<?php

namespace zzbajie\jqueryCropper\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * 资源包
 */
class AvatarAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'statics';
    }

    public $css = [
        'css/cropper.min.css',
        'css/main.css',
    ];

    public $js = [
        'js/cropper.min.js',
        'js/jquery-cropper.min.js',
        'js/main.js'
    ];
}