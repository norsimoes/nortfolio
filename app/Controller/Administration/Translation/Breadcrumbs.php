<?php

namespace Controller\Administration\Translation;

use Lib\App;

/**
 * Breadcrumbs
 *
 * Prepare the translation modules inner breadcrumb navigation.
 */
class Breadcrumbs extends App
{
    protected ?object $_activeModule = null;
    protected array $_moduleData = [];

    /**
     * Class constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        // Get modules data
        $this->_moduleData = (new \Controller\Administration\Translation\Dashboard())->getModuleData();
    }

    /**
     * Index
     *
     * Default class method.
     *
     * @throws \Exception
     */
    public function index(): void
    {
        $this->url->redirect(APP_URL);
    }

    /**
     * Get breadcrumbs
     *
     * Prepare the translations inner breadcrumb navigation.
     *
     * @throws \Exception
     */
    public function getBreadcrumbs(string $callSign = '', int $translationId = 0, int $groupId = 0): string
    {
        $data = [];

        /*
         * Get modules URLs
         */
        $groupUrl = $this->_moduleData['group']->url ?? '';
        $itemUrl = $this->_moduleData['item']->url ?? '';

        /*
         * In translation
         */
        $baseObj = (new \Model\Core\Module())->getByRoute('Administration/Translation');

        if (!$baseObj) return '';

        $data[] = (object) [
            'name' => $baseObj->name,
            'call_sign' => $baseObj->call_sign,
            'url' => $translationId || $callSign != 'Manage' ? APP_URL . $baseObj->url : ''
        ];

        /*
         * In group
         */
        if ($translationId) {

            $translationModel = new \Model\Core\Translation();

            $translationObj = $translationModel->getById($translationId);

            if (!$translationObj) return '';

            $data[] = (object) [
                'name' => $translationObj->value,
                'call_sign' => $translationObj->call_sign,
                'url' => $groupId || $callSign != 'Manage' ? $groupUrl . $translationId . '/' : ''
            ];
        }

        /*
         * In item
         */
        if ($translationId && $groupId) {

            $groupModel = new \Model\Core\TranslationGroup($translationId);

            $groupObj = $groupModel->getById($groupId);

            if (!$groupObj) return '';

            $data[] = (object) [
                'name' => $groupObj->value,
                'call_sign' => $groupObj->call_sign,
                'url' => $callSign != 'Manage' ? $itemUrl . $translationId . '/' . $groupId . '/' : ''
            ];
        }

        // Load view
        return (new \Lib\Loader())->view('Administration/Translation/Breadcrumbs', $data, true);
    }

}
