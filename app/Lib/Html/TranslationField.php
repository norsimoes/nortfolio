<?php

namespace Lib\Html;

/**
 * Translation field
 *
 * Render a translation form element and its translation panel.
 */
class TranslationField
{
    protected array $_i18n = [];
    protected string $_name = '';
    protected string $_type = '';
    protected ?object $_data = null;

    public int $_languageId = 0;
    public ?object $url = null;

    /**
     * Class constructor
     */
    public function __construct(string $name = '', string $type = '', ?object $data = null)
    {
        // Logged user
        $userObj = (\Lib\Session::getInstance())->get('user');

        // I18N
        $this->_languageId = $userObj->language_id ?? APP_I18N_ID;

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('Translation');

        /*
         * Set data
         */
        $this->_name = $name;
        $this->_type = $type;
        $this->_data = $data;
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
    public function render(string $label = '', string $title = '', string $validation = '', string $value = '', array $panelData = []): string
    {
        $returnHtml = '';

        $labelHtml = $label ? '<label title="' . $title . '">' . strtolower($label) . '</label>' : '';

        $required = $validation ? 'required data-validation-error="' . $validation . '"' : '';

        switch ($this->_type) {

            case 'input':
            default: {

                $hiddenHtml = '';

                if (count($panelData) > 0) {

                    foreach ($panelData as $obj) {

                        $hiddenHtml .= '
                        <input type="hidden" name="' . $obj->language_id .  '-' . $this->_name . '" value="' . $obj->value . '" data-iso2="' . $obj->iso2 . '">
                        ';
                    }
                }

                $returnHtml .= '
                <div class="mb-2">
                    ' . $labelHtml . '
                    <div class="input-group">
                        <input type="text" class="form-control" name="' . $this->_data->data_i18n_id . '-' . $this->_name . '" value="' . $value . '" ' . $required . '>
                        <div class="input-group-text translation-trigger" data-bs-toggle="modal" data-bs-target="#j-translation-modal" data-type="input">
                            <span>' . $this->_data->data_i18n_iso2 . '</span>
                        </div>
                        ' . $hiddenHtml . '
                    </div>
                </div>
                ';

                break;
            }

            case 'textarea': {

                $hiddenHtml = '';

                if (count($panelData) > 0) {

                    foreach ($panelData as $obj) {

                        $hiddenHtml .= '
                        <input type="hidden" name="' . $obj->language_id .  '-' . $this->_name . '" value="' . $obj->value . '" data-iso2="' . $obj->iso2 . '">
                        ';
                    }
                }

                $returnHtml .= '
                <div class="mb-2">
                    ' . $labelHtml . '
                    <div class="input-group">
                        <textarea class="form-control" name="' . $this->_data->data_i18n_id . '-' . $this->_name . '" rows="3" ' . $required . '>' . $value . '</textarea>
                        <div class="input-group-text translation-trigger" data-bs-toggle="modal" data-bs-target="#j-translation-modal" data-type="textarea">
                            <span>' . $this->_data->data_i18n_iso2 . '</span>
                        </div>
                        ' . $hiddenHtml . '
                    </div>
                </div>
                ';

                break;
            }

        }

        return $returnHtml;
    }

    /**
     * Process post
     *
     * Process the translation fields on the formulary post.
     */
    public function processPost(string $field = '', bool $matchAvailable = false): array
    {
        $returnArr = [];

        foreach ($_POST as $key => $val) {

            if (str_ends_with($key, '-' . $field)) {

                $xKey = explode('-', $key);

                if ($xKey[0] == $this->_languageId) $defaultVal = $val;

                $returnArr[$xKey[0]] = !empty($val) ? $val : $defaultVal;
            }
        }

        return $returnArr;
    }

}
