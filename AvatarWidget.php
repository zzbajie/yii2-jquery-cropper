<?php

namespace zzbajie\jqueryCropper;

use yii\bootstrap\Widget;
use zzbajie\jqueryCropper\assets\AvatarAsset;

class AvatarWidget extends Widget
{
    public function run()
    {
        AvatarAsset::register($this->view);
        return $this->render('index');
    }
}