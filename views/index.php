<?php

use yii\bootstrap\ActiveForm;
use zzbajie\jqueryCropper\assets\AvatarAsset;

AvatarAsset::register($this);
?>
<div class="modal fade" id="avatar-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class' => 'avatar-form'], 'action' => ['avatar']]) ?>
            <!-- 模态框头部 -->
            <div class="modal-header">
                <h4 class="modal-title">更换头像</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- 模态框主体 -->
            <div class="modal-body">
                <div class="avatar-body">
                    <!-- Upload image and data -->
                    <div class="avatar-upload">
                        <input type="hidden" class="avatar-src" name="avatar_src">
                        <input type="hidden" class="avatar-data" name="avatar_data">
                        <div class="custom-file mb-2">
                            <input type="file" name="avatar_file" class="custom-file-input avatar-input" id="avatarInput" aria-describedby="inputGroupFileAddon01" required>
                            <label class="custom-file-label" for="avatarInput" aria-describedby="inputGroupFileAddon01" data-browse="浏览">选择文件</label>
                            <div class="invalid-feedback">Example invalid custom file feedback</div>
                        </div>
                    </div>
                    <p align="left">允许 JPG, GIF 或 PNG. 最大支持 2MB</p>
                    <!-- Crop and preview -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="avatar-wrapper"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="avatar-preview preview-lg"></div>
                            <div class="avatar-preview preview-md"></div>
                            <div class="avatar-preview preview-sm"></div>
                        </div>
                    </div>
                    <div class="row avatar-btns">
                        <div class="col-md-8">
                            <div class="btn-group">
                                <a href="javascript:;" type="button" class="btn btn-primary" data-method="rotate"
                                   data-option="-90" title="Rotate -90 degrees">左旋转</a>
                                <a href="javascript:;" type="button" class="btn btn-primary" data-method="rotate"
                                   data-option="-15">-15°</a>
                                <a href="javascript:;" type="button" class="btn btn-primary" data-method="rotate"
                                   data-option="-30">-30°</a>
                                <a href="javascript:;" type="button" class="btn btn-primary" data-method="rotate"
                                   data-option="-45">-45°</a>
                            </div>
                            <div class="btn-group">
                                <a href="javascript:;" type="button" class="btn btn-primary" data-method="rotate"
                                   data-option="90" title="Rotate 90 degrees">右旋转</a>
                                <a href="javascript:;" type="button" class="btn btn-primary" data-method="rotate"
                                   data-option="15">15°</a>
                                <a href="javascript:;" type="button" class="btn btn-primary" data-method="rotate"
                                   data-option="30">30°</a>
                                <a href="javascript:;" type="button" class="btn btn-primary" data-method="rotate"
                                   data-option="45">45°</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 模态框底部 -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary avatar-save">保存</button>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
<div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>