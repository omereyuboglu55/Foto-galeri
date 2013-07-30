<?php

/*
 * File: SimpleImage.php
 * Author: Simon Jarvis
 * Düzenleme:Samet ATABAŞ sametatabasch@gmail.com
 *
 */

class SimpleImage
{

    public $image;
    public $image_type;

    /**
     * Resmi yeniden oluşturur image değişkenine atar
     * @param $filename
     * @return bool
     */
    function load($filename)
    {
        $this->image_type = exif_imagetype($filename);
        switch ($this->image_type) {
            case IMAGETYPE_JPEG:
                if ($this->image = imagecreatefromjpeg($filename)) return true; else return false;
                break;
            case IMAGETYPE_PNG:
                if ($this->image = imagecreatefrompng($filename)) return true; else return false;
                break;
        }
    }

    /**
     * Resmi kaydeder
     *
     * @param $filename
     * @param int $compression
     * @param null $permissions
     */
    function save($filename, $compression = 75, $permissions = null)
    {
        switch ($this->image_type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $filename, $compression);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->image, $filename);
                break;
        }
        if ($permissions != null) {

            chmod($filename, $permissions);
        }
    }

    function output($image_type = IMAGETYPE_JPEG)
    {

        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image);
        } elseif ($image_type == IMAGETYPE_GIF) {

            imagegif($this->image);
        } elseif ($image_type == IMAGETYPE_PNG) {

            imagepng($this->image);
        }
    }

    function getWidth()
    {

        return imagesx($this->image);
    }

    function getHeight()
    {

        return imagesy($this->image);
    }

    function resizeToHeight($height)
    {

        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }

    function resize($width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

}

?>