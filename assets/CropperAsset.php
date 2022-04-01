<?php

namespace zzbajie\jqueryCropper\assets;

use yii\web\AssetBundle;

/**
 * Class CropperAsset
 * @package zzbajie\jqueryCropper\assets
 */
class CropperAsset extends AssetBundle
{
    public $sourcePath = '@npm/cropper/dist';

    public $css = [
        'cropper.css',
    ];

    public $js = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $min = YII_ENV_DEV ? '' : '.min';
        $this->js[] = [
            'cropper' . $min . '.js',
        ];
    }

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}