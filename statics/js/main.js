(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        factory(require('jquery'));
    } else {
        factory(jQuery);
    }
})(function ($) {
    'use strict';
    var console = window.console || {
        log: function () {
        }
    };

    function CropAvatar($element) {
        this.$container = $element;
        this.$avatarView = this.$container.find('.avatar-view');
        this.$avatar = this.$avatarView.find('img');
        this.$avatarModal = this.$container.find('#avatar-modal');
        this.$loading = this.$container.find('.loading');
        this.$avatarForm = this.$avatarModal.find('.avatar-form');
        this.$avatarUpload = this.$avatarForm.find('.avatar-upload');
        this.$avatarSrc = this.$avatarForm.find('.avatar-src');
        this.$avatarData = this.$avatarForm.find('.avatar-data');
        this.$avatarInput = this.$avatarForm.find('.avatar-input');
        this.$avatarSave = this.$avatarForm.find('.avatar-save');
        this.$avatarBtns = this.$avatarForm.find('.avatar-btns');
        this.$avatarWrapper = this.$avatarModal.find('.avatar-wrapper');
        this.$avatarPreview = this.$avatarModal.find('.avatar-preview');
        this.init();
    }

    CropAvatar.prototype = {
        constructor: CropAvatar,
        support: {
            fileList: !!$('<input type="file">').prop('files'),
            blobURLs: !!window.URL && URL.createObjectURL,
            formData: !!window.FormData
        },
        init: function () {
            this.support.datauri = this.support.fileList && this.support.blobURLs;
            if (!this.support.formData) {
                console.log('this.support.formData:' + !this.support.formData);
                this.initIframe();
            }
            this.initTooltip();
            this.initModal();
            this.addListener();
        },
        addListener: function () {
            this.$avatarView.on('click', $.proxy(this.click, this));
            this.$avatarInput.on('change', $.proxy(this.change, this));
            //this.$avatarForm.on('submit', $.proxy(this.submit, this));
            this.$avatarSave.on('click', $.proxy(this.submit, this));
            this.$avatarBtns.on('click', $.proxy(this.rotate, this));
        },
        initTooltip: function () {
            this.$avatarView.tooltip({
                placement: 'top'
            });
        },
        initModal: function () {
            this.$avatarModal.modal({
                show: false
            });
        },
        initPreview: function () {
            var url = this.$avatar.attr('src');
            this.$avatarPreview.empty().html('<img src="' + url + '">');
        },
        initIframe: function () {
            console.log('initIframe');
            var target = 'upload-iframe-' + (new Date()).getTime(),
                $iframe = $('<iframe>').attr({
                    name: target,
                    src: ''
                }),
                _this = this;
            $iframe.one('load', function () {
                $iframe.on('load', function () {
                    var data;
                    try {
                        data = $(this).contents().find('body').text();
                    } catch (e) {
                        console.log(e.message);
                    }
                    if (data) {
                        try {
                            data = $.parseJSON(data);
                        } catch (e) {
                            console.log(e.message);
                        }
                        _this.submitDone(data);
                    } else {
                        _this.submitFail('上传失败，请重试');
                    }
                    _this.submitEnd();
                });
            });
            this.$iframe = $iframe;
            this.$avatarForm.attr('target', target).after($iframe.hide());
        },
        click: function () {
            this.$avatarModal.modal('show');
            this.initPreview();
        },
        change: function () {
            var files, file;
            this.removeAlert();
            if (this.support.datauri) {
                console.log('datauri');
                files = this.$avatarInput.prop('files');
                console.log('change:' + files);
                if (files.length > 0) {
                    file = files[0];
                    if (this.isImageFile(file)) {
                        if (this.url) {
                            URL.revokeObjectURL(this.url);
                        }
                        this.url = URL.createObjectURL(file);
                        this.startCropper();
                    } else {
                        this.alert('文件格式不正确001');
                        return false;
                    }
                }
            } else {
                file = this.$avatarInput.val();
                if (this.isImageFile(file)) {
                    this.syncUpload();
                } else {
                    this.alert('文件格式不正确002');
                    return false;
                }
            }
        },
        submit: function () {
            this.removeAlert();
            if (!this.$avatarSrc.val() && !this.$avatarInput.val()) {
                this.alert('请选择本地文件');
                return false;
            }
            if (this.support.formData) {
                this.ajaxUpload();
                return false;
            }
        },
        rotate: function (e) {
            var data;
            if (this.active) {
                data = $(e.target).data();
                if (data.method) {
                    this.$img.cropper(data.method, data.option);
                }
            }
        },
        isImageFile: function (file) {
            if (file.type) {
                return /^image\/\w+$/.test(file.type);
            } else {
                return /\.(jpg|jpeg|png|gif|bmp)$/.test(file);
            }
        },
        startCropper: function () {
            var _this = this;
            if (this.active) {
                this.$img.cropper('replace', this.url);
            } else {
                this.$img = $('<img src="' + this.url + '">');
                this.$avatarWrapper.empty().html(this.$img);
                this.$img.cropper({
                    aspectRatio: 1,
                    preview: '.avatar-preview',
                    strict: false,
                    minCropBoxWidth: 100,
                    minCropBoxHeight: 100,
                    crop: function (data) {
                        var json = ['{"x":' + data.detail.x, '"y":' + data.detail.y, '"height":' + data.detail.height, '"width":' + data.detail.width, '"rotate":' + data.detail.rotate + '}'].join();
                        _this.$avatarData.val(json);
                    }
                });
                this.active = true;
            }
        },
        stopCropper: function () {
            if (this.active) {
                this.$img.cropper('destroy');
                this.$img.remove();
                this.active = false;
            }
        },
        ajaxUpload: function () {
            console.log('ajaxUpload->this.active:' + this.active);
            if (this.active) {
                this.removeAlert();
                var url = this.$avatarForm.attr('action'),
                    data = new FormData(this.$avatarForm[0]),
                    _this = this;
                $.ajax(url, {
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        _this.submitStart();
                    },
                    success: function (data) {
                        _this.submitDone(data);
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        _this.submitFail(textStatus || errorThrown);
                    },
                    complete: function () {
                        _this.submitEnd();
                    }
                });
            }
        },
        syncUpload: function () {
            this.$avatarSave.click();
        },
        submitStart: function () {
            this.$avatarSave.attr('disabled', 'disabled');
            this.$loading.fadeIn();
        },
        submitDone: function (data) {
            console.log('submitDone:' + data);
            if ($.isPlainObject(data) && data.code === 200) {
                if (data.url) {
                    this.url = data.url;
                    if (this.support.datauri || this.uploaded) {
                        this.uploaded = false;
                        this.cropDone();
                    } else {
                        this.uploaded = true;
                        this.$avatarSrc.val(this.url);
                        this.startCropper();
                    }
                    this.$avatarInput.val('');
                } else if (data.message) {
                    this.alert(data.message);
                }
            } else {
                this.alert('网络异常 Failed to response');
            }
        },
        submitFail: function (msg) {
            this.alert(msg);
        },
        submitEnd: function () {
            this.$avatarSave.removeAttr('disabled');
            this.$loading.fadeOut();
        },
        cropDone: function () {
            this.$avatarForm.get(0).reset();
            this.$avatar.attr('src', this.url);
            this.stopCropper();
            this.$avatarModal.modal('hide');
        },
        alert: function (msg) {
            var $alert = ['<div class="alert alert-danger avater-alert">', '<button onclick="$(this).parent().hide();" type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>', msg, '</div>'].join('');
            if (this.$avatarForm.find('.avater-alert').length > 0) {
                this.$avatarForm.find('.avater-alert').remove();
            }
            this.$avatarUpload.after($alert);
        },
        removeAlert: function () {
            if (this.$avatarForm.find('.avater-alert').length > 0) {
                this.$avatarForm.find('.avater-alert').remove();
            }
        }
    };
    $(function () {
        return new CropAvatar($('.crop-avatar'));
    });
});