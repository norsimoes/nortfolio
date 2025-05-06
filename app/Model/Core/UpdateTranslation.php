<?php

namespace Model\Core;

use Lib\MySql;

/**
 * Update translation
 *
 * Data handler for the MySql table with the same name as this class.
 */
class UpdateTranslation extends MySql
{
    private string $_dbConn;

    /**
     * Class constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        // Set database connector
        $this->_dbConn = 'core';

        // Initialize database connector
        parent::__construct($this->_dbConn);
    }

    /**
     * Update translation
     *
     * Replicate all translation records in all databases to a given language id.
     *
     * @throws \Exception
     */
    public function updateTranslation(int $originLanguageId = 0, int $targetLanguageId = 0, string $database = ''): int
    {
        $errorCount = 0;

        /*
         * I18n text small
         */
        $originSmall = $this->_getSmall($originLanguageId, $database . '.i18n__text__small');

        if ($originSmall) {

            foreach ($originSmall as $small) {

                $targetSmall = $this->_checkSmall($small->sid, $targetLanguageId, $database . '.i18n__text__small');

                if (!$targetSmall) {

                    if (!$this->_addSmall($small->sid, $targetLanguageId, $small->value, $small->slug, $database . '.i18n__text__small')) $errorCount ++;
                }
            }
        }

        /*
         * I18n text medium
         */
        $originMedium = $this->_getMedium($originLanguageId, $database . '.i18n__text__medium');

        if ($originMedium) {

            foreach ($originMedium as $medium) {

                $targetMedium = $this->_checkMedium($medium->mid, $targetLanguageId, $database . '.i18n__text__medium');

                if (!$targetMedium) {

                    if (!$this->_addMedium($medium->mid, $targetLanguageId, $medium->value, $database . '.i18n__text__medium')) $errorCount ++;
                }
            }
        }

        /*
         * I18n text large
         */
        $originLarge = $this->_getLarge($originLanguageId, $database . '.i18n__text__large');

        if ($originLarge) {

            foreach ($originLarge as $large) {

                $targetLarge = $this->_checkLarge($large->lid, $targetLanguageId, $database . '.i18n__text__large');

                if (!$targetLarge) {

                    if (!$this->_addLarge($large->lid, $targetLanguageId, $large->value, $database . '.i18n__text__large')) $errorCount ++;
                }
            }
        }

        return $errorCount;
    }

    /**
     * Get small
     */
    private function _getSmall(int $originLanguageId = 0, string $database = ''): array
    {
        $query = "SELECT * FROM $database WHERE `language_id` = ?";

        $parameters = [$originLanguageId];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Check small
     */
    private function _checkSmall(int $sid = 0, int $targetLanguageId = 0, string $database = ''): object|bool
    {
        $query = "SELECT * FROM $database WHERE `sid` = ? AND `language_id` = ?";

        $parameters = [$sid, $targetLanguageId];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Add small
     *
     * @throws \Exception
     */
    private function _addSmall(int $sid = 0, int $targetLanguageId = 0, string $value = '', string $slug = '', string $database = ''): int
    {
        $query = "
        INSERT INTO $database(
            `id`, `sid`, `language_id`, `value`, `slug`
        )
        VALUES (
            NULL, ?, ?, ?, ?
        )";

        $parameters = [$sid, $targetLanguageId, $value, $slug];

        return $this->insert($query, $parameters);
    }

    /**
     * Get medium
     */
    private function _getMedium(int $originLanguageId = 0, string $database = ''): array
    {
        $query = "SELECT * FROM $database WHERE `language_id` = ?";

        $parameters = [$originLanguageId];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Check medium
     */
    private function _checkMedium(int $mid = 0, int $targetLanguageId = 0, string $database = ''): object|bool
    {
        $query = "SELECT * FROM $database WHERE `mid` = ? AND `language_id` = ?";

        $parameters = [$mid, $targetLanguageId];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Add medium
     *
     * @throws \Exception
     */
    private function _addMedium(int $mid = 0, int $targetLanguageId = 0, string $value = '', string $database = ''): int
    {
        $query = "
        INSERT INTO $database(
            `id`, `mid`, `language_id`, `value`
        )
        VALUES (
            NULL, ?, ?, ?
        )";

        $parameters = [$mid, $targetLanguageId, $value];

        return $this->insert($query, $parameters);
    }

    /**
     * Get large
     */
    private function _getLarge(int $originLanguageId = 0, string $database = ''): array
    {
        $query = "SELECT * FROM $database WHERE `language_id` = ?";

        $parameters = [$originLanguageId];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Check large
     */
    private function _checkLarge(int $lid = 0, int $targetLanguageId = 0, string $database = ''): object|bool
    {
        $query = "SELECT * FROM $database WHERE `lid` = ? AND `language_id` = ?";

        $parameters = [$lid, $targetLanguageId];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Add large
     *
     * @throws \Exception
     */
    private function _addLarge(int $lid = 0, int $targetLanguageId = 0, string $value = '', string $database = ''): int
    {
        $query = "
        INSERT INTO $database(
            `id`, `lid`, `language_id`, `value`
        )
        VALUES (
            NULL, ?, ?, ?
        )";

        $parameters = [$lid, $targetLanguageId, $value];

        return $this->insert($query, $parameters);
    }

    /**
     * Count translation
     */
    public function countTranslation(string $database = '', int $languageId = APP_I18N_ID): int
    {
        $querySmall = "SELECT COUNT(*) FROM `$database`.`i18n__text__small` WHERE `language_id` = ?";

        $countSmall = (int) $this->fetchValue($querySmall, [$languageId]);

        $queryMedium = "SELECT COUNT(*) FROM `$database`.`i18n__text__medium` WHERE `language_id` = ?";

        $countMedium = (int) $this->fetchValue($queryMedium, [$languageId]);

        $queryLarge = "SELECT COUNT(*) FROM `$database`.`i18n__text__large` WHERE `language_id` = ?";

        $countLarge = (int) $this->fetchValue($queryLarge, [$languageId]);

        return $countSmall + $countMedium + $countLarge;
    }

    /**
     * Update module route
     *
     * Replicate all module routes to a given language id.
     *
     * @throws \Exception
     */
    public function updateModuleRoute(int $originLanguageId = 0, int $targetLanguageId = 0): int
    {
        $errorCount = 0;

        $originRoute = $this->_getRoute($originLanguageId);

        if ($originRoute) {

            foreach ($originRoute as $route) {

                $targetRoute = $this->_checkRoute($route->module_id, $targetLanguageId);

                if (!$targetRoute) {

                    $errorCount = $this->_addRoute($route->module_id, $originLanguageId, $targetLanguageId);
                }
            }
        }

        return $errorCount;
    }

    /**
     * Get route
     */
    private function _getRoute(int $originLanguageId = 0): array
    {
        $query = "SELECT * FROM `module__route` WHERE `language_id` = ?";

        $parameters = [$originLanguageId];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Check route
     */
    private function _checkRoute(int $moduleId = 0, int $targetLanguageId = 0): object|bool
    {
        $query = "SELECT * FROM `module__route` WHERE `module_id` = ? AND `language_id` = ?";

        $parameters = [$moduleId, $targetLanguageId];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Add route
     *
     * @throws \Exception
     */
    private function _addRoute(int $moduleId = 0, int $originLanguageId = 0, int $targetLanguageId = 0): int
    {
        $errorCount = 0;

        $moduleModel = new \Model\Core\Module();

        $raw = $moduleModel->getRawRoute($moduleId, $originLanguageId);

        if (!$moduleModel->addRoute($moduleId, $targetLanguageId, $raw->route, $raw->slug)) $errorCount ++;

        return $errorCount;
    }

    /**
     * Count route
     */
    public function countRoute(int $languageId = APP_I18N_ID): int
    {
        $query = "SELECT COUNT(*) FROM `module__route` WHERE `language_id` = ?";

        $parameters = [$languageId];

        return (int) $this->fetchValue($query, $parameters);
    }

}
