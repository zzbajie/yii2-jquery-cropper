<?php
namespace zzbajie\jqueryCropper\assets;
use yii\web\AssetBundle;

/**
 * yii2-avatar 资源包
 * @author opqnext
 * @date 2018-04-20
 */
class AvatarAsset extends AssetBundle
{
    public $css = [
        'css/cropper.min.css',
        'css/main.css',
    ];

    public $js = [
        'js/cropper.min.js',
        'js/main.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD,
    ];

    public function init()
    {
        $this->sourcePath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR . 'statics';
    }


}