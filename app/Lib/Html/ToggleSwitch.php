<?php

namespace Lib\Html;

/**
 * Toggle switch
 *
 * Output a button that toggles a record parameter.
 */
class ToggleSwitch
{
    protected array $_i18n = [];
    protected int $_id = 0;
    protected ?object $_modelClass = null;

    public $_session;
    public $url;

    /**
     * Class constructor
     */
    public function __construct(int $id = 0, string $modelClass = '')
    {
        // Load session
        $this->_session = new \Lib\Session();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('ToggleSwitch');

        $this->_id = $id;
        $this->_modelClass = new $modelClass();
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
     * Renders the status toggle button.
     */
    public function render(mixed $active = '', mixed $blocked = '', string $target = '', string $method = 'getStatus'): string
    {
        $returnHtml = '';

        if (!isset($active) || !isset($blocked)) return $returnHtml;

        // Get current status
        $currentStatus = $this->_modelClass->$method($this->_id);

        if (!isset($currentStatus)) return $returnHtml;

        /*
         * Render button
         */
        $icon = $currentStatus == $active ? 'toggle-on' : 'toggle-off';
        $title = $currentStatus == $active ? $this->_i18n['Toggler'][1] : $this->_i18n['Toggler'][2];

        $link = new \Lib\Html\A();

        $link->setAttr('class', "btn pt-0 pb-0 text-secondary j-toggle-switch");
        $link->setAttr('title', $title);
        $link->setAttr('data-id', $this->_id);
        $link->setAttr('data-url', $target . $this->_id . '/');
        $link->setAttr('data-status', $currentStatus);
        $link->setAttr('data-active', $active);
        $link->setAttr('data-blocked', $blocked);
        $link->setAttr('data-icon-' . $active, "toggle-on");
        $link->setAttr('data-icon-' . $blocked, "toggle-off");
        $link->setAttr('data-title-' . $active, $this->_i18n['Toggler'][1]);
        $link->setAttr('data-title-' . $blocked, $this->_i18n['Toggler'][2]);
        $link->setAttr('data-msg-wrong-response', $this->_i18n['AjaxError'][1]);
        $link->setAttr('data-msg-ajax-fail', $this->_i18n['AjaxError'][2]);
        $link->setContent('<span class="' . $icon . '"></span>');

        $returnHtml .= $link->render();

        return $returnHtml;
    }

    /**
     * Render disabled
     *
     * Renders the disabled status toggle button.
     */
    public function renderDisabled(mixed $active = '', mixed $blocked = '', string $method = 'getStatus'): string
    {
        $returnHtml = '';

        if (!isset($active) || !isset($blocked)) return $returnHtml;

        // Get current status
        $currentStatus = $this->_modelClass->$method($this->_id);

        if (!isset($currentStatus)) return $returnHtml;

        /*
         * Render disabled button
         */
        $icon = $currentStatus == $active ? 'toggle-on toggle-disabled' : 'toggle-off toggle-disabled';
        $title = $currentStatus == $active ? $this->_i18n['Toggler'][3] : $this->_i18n['Toggler'][4];

        $link = new \Lib\Html\A();

        $link->setAttr('class', "btn pt-0 pb-0 j-toggle-disabled");
        $link->setAttr('title', $title);
        $link->setContent('<span class="' . $icon . '"></span>');

        $returnHtml .= $link->render();

        return $returnHtml;
    }

    /**
     * Update status
     *
     * Updates the status of a record.
     */
    public function updateStatus(mixed $newStatus = '', string $method = 'setStatus'): string
    {
        if ($this->_modelClass->$method($this->_id, $newStatus)) {

            // Set a success message
            $status = 'success';
            $message = $this->_i18n['MsgSuccess'][1];

        } else {

            // Set an error message
            $status = 'error';
            $message = $this->_i18n['MsgError'][1];
        }

        /*
         * Return data
         */
        $returnArr = [
            'status' => $status,
            'message' => $message
        ];

        echo json_encode($returnArr);
        exit();
    }

}
