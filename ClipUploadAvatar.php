<?php

namespace zzbajie\jqueryCropper;

/**
 * 裁剪图片类
 * Class ClipUploadAvatar
 * @package zzbajie\jqueryCropper
 */
class ClipUploadAvatar
{
    private $src;
    private $data;
    private $dst;
    private $type;
    private $extension;
    private $msg;

    public function __construct($data, $file)
    {
        $this->setData($data);
        $this->setSrc($file);
        $this->crop($this->data, $this->src);
    }

    public function setData($data)
    {
        if (!empty($data)) {
            $this->data = json_decode(stripslashes($data));
        }
    }

    public function setSrc($file)
    {
        $errorCode = $file['error'];
        if ($errorCode === UPLOAD_ERR_OK) {
            $type = exif_imagetype($file['tmp_name']);
            if ($type) {
                //$extension = image_type_to_extension($type);
                if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG || $type == IMAGETYPE_BMP) {
                    $this->src = $file['tmp_name'];
                    $this->type = $type;
                    $this->extension = '.jpg'; //固定采用.jpg
                } else {
                    $this->msg = '请上传以下类型的图片：JPG, PNG, GIF,BMP';
                }
            } else {
                $this->msg = '请上传图片文件';
            }
        } else {
            $this->msg = $this->codeToMessage($errorCode);
        }
    }

    public function crop($data, $src)
    {

        if (!empty($data) && !empty($src)) {
            switch ($this->type) {
                case IMAGETYPE_GIF:
                    $src_img = imagecreatefromgif($src);
                    break;

                case IMAGETYPE_JPEG:
                    $src_img = imagecreatefromjpeg($src);
                    break;

                case IMAGETYPE_PNG:
                    $src_img = imagecreatefrompng($src);
                    break;

                case IMAGETYPE_BMP:
                    $src_img = imagecreatefrombmp($src);
                    break;
            }

            if (!$src_img) {
                $this->msg = "无法读取图像文件";
                return;
            }

            $size = getimagesize($src);
            $size_w = $size[0]; // natural width
            $size_h = $size[1]; // natural height

            $src_img_w = $size_w;
            $src_img_h = $size_h;

            $degrees = $data->rotate;

            // Rotate the source image
            if (is_numeric($degrees) && $degrees != 0) {
                // PHP's degrees is opposite to CSS's degrees
                $new_img = imagerotate($src_img, -$degrees, imagecolorallocatealpha($src_img, 0, 0, 0, 127));

                imagedestroy($src_img);
                $src_img = $new_img;

                $deg = abs($degrees) % 180;
                $arc = ($deg > 90 ? (180 - $deg) : $deg) * M_PI / 180;

                $src_img_w = $size_w * cos($arc) + $size_h * sin($arc);
                $src_img_h = $size_w * sin($arc) + $size_h * cos($arc);

                // Fix rotated image miss 1px issue when degrees < 0
                $src_img_w -= 1;
                $src_img_h -= 1;
            }

            $tmp_img_w = $data->width;
            $tmp_img_h = $data->height;
            $dst_img_w = 200;
            $dst_img_h = 200;

            $src_x = $data->x;
            $src_y = $data->y;

            if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
                $src_x = $src_w = $dst_x = $dst_w = 0;
            } else if ($src_x <= 0) {
                $dst_x = -$src_x;
                $src_x = 0;
                $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
            } else if ($src_x <= $src_img_w) {
                $dst_x = 0;
                $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
            }

            if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
                $src_y = $src_h = $dst_y = $dst_h = 0;
            } else if ($src_y <= 0) {
                $dst_y = -$src_y;
                $src_y = 0;
                $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
            } else if ($src_y <= $src_img_h) {
                $dst_y = 0;
                $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
            }

            // Scale to destination position and size
            $ratio = $tmp_img_w / $dst_img_w;
            $dst_x /= $ratio;
            $dst_y /= $ratio;
            $dst_w /= $ratio;
            $dst_h /= $ratio;

            $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

            // Add transparent background to destination image
            imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
            imagesavealpha($dst_img, true);

            $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
            if ($result) {
                $this->dst =  $dst_img;
            } else {
                $this->msg = "无法裁剪图像文件";
            }
            imagedestroy($src_img);
            //imagedestroy($dst_img);
        }
    }

    public function codeToMessage($code)
    {
        $errors = array(
            UPLOAD_ERR_INI_SIZE => '上载的文件超出了中的upload_max_filesize指令php.ini文件',
            UPLOAD_ERR_FORM_SIZE => '上载的文件超出了在HTML表单中指定的MAX_file_SIZE指令',
            UPLOAD_ERR_PARTIAL => '上载的文件仅部分上载',
            UPLOAD_ERR_NO_FILE => '未上载任何文件',
            UPLOAD_ERR_NO_TMP_DIR => '缺少临时文件夹',
            UPLOAD_ERR_CANT_WRITE => '无法将文件写入磁盘',
            UPLOAD_ERR_EXTENSION => '文件上载被扩展名停止',
        );

        if (array_key_exists($code, $errors)) {
            return $errors[$code];
        }

        return '未知上载错误';
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function getResult()
    {
        return !empty($this->data) ? $this->dst : $this->src;
    }

    public function getMsg()
    {
        return $this->msg;
    }
}
