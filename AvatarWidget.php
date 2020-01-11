<?php

namespace zzbajie\jqueryCropper;

use zzbajie\jqueryCropper\assets\AvatarAsset;
use yii\bootstrap\Widget;

class AvatarWidget extends Widget
{
    public $imageUrl = '';

    public function run()
    {
        AvatarAsset::register($this->view);
        return $this->render('index', ['imageUrl' => $this->imageUrl]);
    }
}