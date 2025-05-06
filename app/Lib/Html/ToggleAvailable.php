<?php
/**
 * Toggle available class.
 */
namespace Lib\Html;

/**
 * Toggle switch
 *
 * Output a button that toggles a record parameter.n.pt>
 */
class ToggleAvailable
{
    /**
     * Vocabulary data
     *
     * @var array
     */
    protected $_i18n = [];

    public $_session;
    public $_id;
    public $_modelClass;
    public $url;

    /**
     * Class Constructor
     *
     * @param integer $id
     * @param string $modelClass
 	 * @return void
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
     *
     * @return void
     */
    public function index()
    {
        $this->url->redirect(APP_URL);
    }

    /**
     * Render
     *
     * Renders the status toggle button.
     *
     * @param mixed $active
     * @param mixed $blocked
     * @param string $target
     * @param string $method
     * @return string
     */
    public function render($active = '', $blocked = '', $getUrl = '', $postUrl = '', $method = 'getStatus')
    {
        $returnHtml = '';

        if (!isset($active) || !isset($blocked)) return;

        // Get current status
        $currentStatus = $this->_modelClass->$method($this->_id);

        if (!isset($currentStatus)) return;

        /*
         * Render button
         */
        $icon = $currentStatus == $active ? 'toggle-on' : 'toggle-off';
        $title = $currentStatus == $active ? $this->_i18n['Toggler'][1] : $this->_i18n['Toggler'][2];

        $link = new \Lib\Html\A();

        $link->setAttr('data-toggle', 'modal');
        $link->setAttr('data-target', '#j-activation-modal');
        $link->setAttr('data-icon', 'fas fa-info');
        $link->setAttr('data-color', 'primary');
        $link->setAttr('data-title', 'modal title');
        $link->setAttr('data-text', 'modal text');
        $link->setAttr('data-submit-color', 'primary');
        $link->setAttr('data-submit-label', $this->_i18n['Toggler'][5]);
        $link->setAttr('data-cancel-label', $this->_i18n['Toggler'][6]);

        $link->setAttr('class', "btn pt-0 pb-0 text-secondary j-toggle-available");
        $link->setAttr('title', $title);
        $link->setAttr('data-id', $this->_id);
        $link->setAttr('data-get-url', $getUrl);
        $link->setAttr('data-post-url', $postUrl . $this->_id . '/');
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
     *
     * @param mixed $active
     * @param mixed $blocked
     * @param string $method
     * @return string
     */
    public function renderDisabled($active = '', $blocked = '', $method = 'getStatus')
    {
        $returnHtml = '';

        if (!isset($active) || !isset($blocked)) return;

        // Get current status
        $currentStatus = $this->_modelClass->$method($this->_id);

        if (!isset($currentStatus)) return;

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
     *
     * @param string $newStatus
     * @param string $method
     * @return string
     */
    public function updateStatus($newStatus = '', $method = 'setStatus')
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

        if ($this->_session->isAjax()) {
            echo json_encode($returnArr);
            exit();
        }

        return json_encode($returnArr);
    }

}
