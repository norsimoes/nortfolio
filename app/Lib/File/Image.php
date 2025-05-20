<?php

namespace Lib\File;

/**
 * Image
 *
 * Handles image file uploads
 */
class Image
{

    public $error = '';
    public $fileArr = [];
    public $path = '';
    public $name = '';
    public $thumb = '';
    public $overwrite = '';
    public $newName = '';

    public function __construct(array $fileArr = [], string $path = '', string $name = '', int $thumb = 0, bool $overwrite = false)
    {
        require_once APP_BASEPATH_LIB . 'Verot/' . DIRECTORY_SEPARATOR . 'class.upload.php';

        if (! class_exists('upload')) {
            throw new \Exception('Vendor not found!');
        }

        if (empty($fileArr) || empty($path)) {
            throw new \Exception('The field name and target path are missing!');
        }

        $this->fileArr = $fileArr;
        $this->path = $path;
        $this->name = $name;
        $this->thumb = $thumb;
        $this->overwrite = $overwrite;

        $this->_process();
    }

    private function _process()
    {
        $handle = new \Upload($this->fileArr);

        if (! $handle->uploaded) {

            $this->error = 'Image upload error: ' . $handle->error;
        }

        $handle->file_new_name_body = $this->name;
        $this->newName = $this->name . '.' . $handle->file_src_name_ext;

        $handle->file_overwrite = $this->overwrite;

        $handle->Process($this->path);

        // Get the file name that was written on the CDN
        $this->newName = $handle->file_dst_name;

        if (! $handle->processed) {

            $this->error = 'Image error: ' . $handle->error;
        }

        if ($this->thumb) {

            // Thumbnail
            $handle->image_resize = true;
            $handle->image_ratio_crop = true;

            $handle->image_x = $this->thumb;
            $handle->image_y = $this->thumb;

            $handle->file_new_name_body = $this->name;

            $handle->Process($this->path . 'thumb' . DIRECTORY_SEPARATOR);

            if (! $handle->processed) {

                $this->error = 'Thumbnail error: ' . $handle->error;
            }
        }

        $handle->Clean();

        $this->fileArr = [];
        $this->path = '';
        $this->name = '';
        $this->thumb = 0;
    }

}
