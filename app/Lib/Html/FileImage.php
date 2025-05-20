<?php

namespace Lib\Html;

/**
 * File image
 *
 * Render an image container.
 */
class FileImage
{
    protected ?object $_activeModule = null;
    protected array $_i18n = [];

    public int $_languageId = 0;
    public ?object $url = null;

    /**
     * Class constructor
     */
    public function __construct(string $name = '', string $type = '', object $data = null)
    {
		// Active module
        $this->_activeModule = (new \Model\Core\Module())->getByRoute((\Lib\Router::getInstance())->getDbRoute());

        // Logged user
        $userObj = (\Lib\Session::getInstance())->get('user');

        // I18N
        $this->_languageId = $userObj->language_id ?? APP_I18N_ID;

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('Core');

        /*
         * Set data
         */

    }

    /**
     * Index
     *
     * Default class method.
     */
    public function index(): void
    {
        $this->url->redirect(APP_URL);
    }

    /**
     * Render
     *
     * Renders a translation form element.
     */
    public function render(string $name = '', string $src = '', string $label = '', string $title = '', string $deleteUrl = ''): string
    {
        $returnHtml = '';

        if ($name) {

            $returnHtml = '
            <div class="mb-2">
                <label class="" title="' . $title . '">' . strtolower($label) . '</label>
                <div class="file-image">
                    <img class="file-image-preview" src="' . $src . '">
                    <div class="file-image-close" data-assets="' . APP_URL_ASSETS . '" data-delete="' . $deleteUrl . '" title="' . $this->_i18n['FileImage'][1] . '">
                        <span class="fa-stack fa-2x">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="fas fa-times fa-stack-1x fa-inverse"></i>
                        </span>
                    </div>
                </div>
                <input class="file-image-input" type="file" name="' . $name . '">
            </div>
            ';
        }

        return $returnHtml;
    }

    /**
     * Process file
     *
     * Upload an image file to the cdn folder and return its name.
     */
    public function processFile(string $postName = '', string $cdnFolder = '', string $oldFilename = ''): string
    {
        $file = $_FILES[$postName] ?? '';

        if (empty($file['name'])) {

            return $oldFilename;

        } else {

            $targetPath = APP_BASEPATH_CDN . $cdnFolder;
            $fileInfo = pathinfo($file['name']);
            $extension = $fileInfo['extension'];
            $newName = md5(time() . $fileInfo['filename']);
            $targetFilename = $newName . '.' . $extension;

            move_uploaded_file($file['tmp_name'], $targetPath . $targetFilename);
            unlink($targetPath . $oldFilename);
        }

        return $targetFilename;
    }

}
