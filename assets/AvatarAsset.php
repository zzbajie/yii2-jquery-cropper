<?php

namespace zzbajie\jqueryCropper\assets;

use yii\web\AssetBundle;

/**
 * Class AvatarAsset
 * @package zzbajie\jqueryCropper\assets
 */
class AvatarAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'statics';
    }

    public $css = [
        'css/main.css',
    ];

    public $js = [
        'js/main.js'
    ];

    public $depends = [
        'zzbajie\jqueryCropper\assets\CropperAsset',
    ];
}