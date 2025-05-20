<?php

namespace Model\Core;

use Lib\MySql;

/**
 * Module
 *
 * Data handler for the MySql table with the same name as this class.
 */
class Module extends MySql
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
     * Count
     *
     * Count all available records.
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM `module`";

        return (int) $this->fetchValue($query);
    }

    /**
     * Count by parent id
     *
     * Count all modules with the same parent id.
     */
    public function countByParentId(int $parentModuleId = 0): int
    {
        $query = "SELECT COUNT(*) as total FROM `module` WHERE `parent_module_id` = ?";

        $parameters = [$parentModuleId];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Get all recursive
     *
     * Retrieve the complete module tree.
     */
    public function getAllRecursive(int $parentModuleId = 0): array
    {
        $moduleArr = $this->getAllByParentId(0, 0, '', 'position ASC', null, $parentModuleId);

        foreach ($moduleArr as $module) {

            $child = ($module->child_count > 0) ? $this->getAllRecursive($module->module_id) : '';

            $returnArr[$module->module_id] = [
                'module_id' => $module->module_id,
                'name' => $module->name,
                'child_count' => $module->child_count,
                'child' => $child
            ];
        }

        return $returnArr ?? [];
    }

    /**
     * Get all by parent id
     *
     * Select all available records using their parent id.
     */
    public function getAllByParentId(
        int $offset = 0,
        int $rowCount = 0,
        string $search = '',
        string $multiSort = 'position ASC',
        ?array $multiFilter = null,
        int $parentId = 0
        ): array {

        $query = "
        SELECT
            t1.`module_id`,
            t1.`parent_module_id`,
            t1.`call_sign`,
            t1.`icon`,
            t1.`is_active`,
            t1.`position`,

            `name_i18n`.`value` AS 'name',
            `desc_i18n`.`value` AS 'desc',

            `module__route`.`route`,
            `module__route`.`slug`,
            CONCAT(`module__route`.`slug`, '/') AS 'url',

            (
                SELECT COUNT(*)
                FROM `module` t2
                WHERE t2.`parent_module_id` = t1.`module_id`
            ) AS 'child_count'

        FROM `module` t1

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = t1.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = t1.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
        )

        LEFT JOIN `module__route` ON (
            `module__route`.`module_id` = t1.`module_id`
            AND
            `module__route`.`language_id` = ?
        )

        WHERE t1.`parent_module_id` = ?
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $parentId
        ];

        /*
         * Search
         */
        if (!empty($search)) {

            $query .= "
            AND (
                t1.`module_id` LIKE ?
                OR
                `name_i18n`.`value` LIKE ?
                OR
                `module__route`.`route` LIKE ?
                OR
                `module__route`.`slug` LIKE ?
            )
            ";

            $parameters[] = '%' . $search . '%';
            $parameters[] = '%' . $search . '%';
            $parameters[] = '%' . $search . '%';
            $parameters[] = '%' . $search . '%';
        }

        /*
         * Columns filtering
         */
        if (is_array($multiFilter) && count($multiFilter) >= 1) {
            foreach ($multiFilter as $filterName => $filterValue) {
                $query .= " AND " . $filterName . " = ? ";
                $parameters[] = $filterValue;
            }
        }

        // Multi sort
        if (!empty($multiSort)) $query .= " ORDER BY " . $multiSort;

        // Offset
        if ($rowCount > 0) $query .= " LIMIT " . $offset . "," . $rowCount;

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Get data object
     *
     * Retrieve the data object of a module record.
     */
    public function getDataObject(int $moduleId = 0): ?object
    {
        // Load model class
        $languageModel = new \Model\Core\Language();

        // Module object
        $rowObj = $this->getById($moduleId);

        /*
         * Translation languages
         */
        $languagesArr = $languageModel->getActive();

        $nameData = [];
        $descData = [];

        foreach ($languagesArr as $language) {

            if ($language->language_id == $this->_languageId) continue;
            // $default = $language->language_id == $this->_languageId ? true : false;

            $nameData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_name', $moduleId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
                'required' => 'required'
                // 'default' => $default
            ];

            $descData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('mid_desc', $moduleId, 'i18n__text__medium', 'mid', $language->language_id) ?? '',
                'required' => ''
                // 'default' => $default
            ];
        }

        /*
         * Return data
         */
        return (object) [
            'module_id' => $rowObj->module_id ?? 0,
            'parent_module_id' => $rowObj->parent_module_id ?? 0,
            'call_sign' => $rowObj->call_sign ?? '',
            'route' => $rowObj->route ?? '',
            'icon' => $rowObj->icon ?? 'fas fa-th-large',
            'is_active' => $rowObj->is_active ?? 1,
            'data_i18n_id' => $this->_languageId ?? 0,
            'data_i18n_iso2' => $languageModel->getIso2ById($this->_languageId) ?? '',
            'name' => $this->_getTranslation('sid_name', $moduleId, 'i18n__text__small', 'sid', $this->_languageId) ?? '',
            'name_i18n' => $nameData,
            'desc' => $this->_getTranslation('mid_desc', $moduleId, 'i18n__text__medium', 'mid', $this->_languageId) ?? '',
            'desc_i18n' => $descData
        ];
    }

    /**
     * Get move data
     *
     * Retrieve the module name array for module moving purposes.
     */
    public function getMoveData(int $moduleId = 0): ?object
    {
        // Load model class
        $languageModel = new \Model\Core\Language();

        // Module object
        $rowObj = $this->getById($moduleId);

        /*
         * Translation languages
         */
        $languagesArr = $languageModel->getActive();

        $nameData = [];

        foreach ($languagesArr as $language) {

            $nameData[$language->language_id] = $this->_getTranslation('sid_name', $moduleId, 'i18n__text__small', 'sid', $language->language_id);
        }

        /*
         * Return data
         */
        return (object) [
            'module_id' => $rowObj->module_id ?? 0,
            'call_sign' => $rowObj->call_sign ?? '',
            'name_arr' => $nameData,
        ];
    }

    /**
     * Get export data
     *
     * Retrieve module data for exporting purposes.
     */
    public function getExportData(int $moduleId = 0): ?object
    {
        // Load model class
        $languageModel = new \Model\Core\Language();

        // Module object
        $rowObj = $this->getById($moduleId);

        /*
         * Translation languages
         */
        $languagesArr = $languageModel->getActive();

        $nameArr = [];
        $descArr = [];

        foreach ($languagesArr as $language) {

            $nameArr[$language->language_id] = $this->_getTranslation('sid_name', $moduleId, 'i18n__text__small', 'sid', $language->language_id);
            $descArr[$language->language_id] = $this->_getTranslation('mid_desc', $moduleId, 'i18n__text__medium', 'mid', $language->language_id);
        }

        /*
         * Return data
         */
        return (object) [
            'module_id' => $rowObj->module_id ?? 0,
            'call_sign' => $rowObj->call_sign ?? '',
            'name_arr' => $nameArr ?: [],
            'desc_arr' => $descArr ?: [],
            'icon' => $rowObj->icon ?? '',
            'is_active' => $rowObj->is_active ?? 0,
            'position' => $rowObj->position ?? 0,
        ];
    }

    /**
     * Get translation data
     *
     * Retrieve the module name array for module translation purposes.
     */
    public function getTranslationData(): array
    {
        $returnArr = [];

        // Load model class
        $languageModel = new \Model\Core\Language();

        // Module object
        $rowObj = $this->getAllRaw();

        // Translation languages
        $languagesArr = $languageModel->getActive();

        foreach ($rowObj as $row) {

            $nameData = [];
            $descData = [];

            foreach ($languagesArr as $language) {

                $nameData[] = (object) [
                    'language_id' => $language->language_id,
                    'iso2' => $language->iso2,
                    'value' => $this->_getTranslation('sid_name', $row->module_id, 'i18n__text__small', 'sid', $language->language_id) ?? '',
                ];

                $descData[] = (object) [
                    'language_id' => $language->language_id,
                    'iso2' => $language->iso2,
                    'value' => $this->_getTranslation('mid_desc', $row->module_id, 'i18n__text__medium', 'mid', $language->language_id) ?? '',
                ];
            }

            /*
             * Return data
             */
            $returnArr[$row->module_id] = (object) [
                'module_id' => $row->module_id ?? 0,
                'call_sign' => $row->call_sign ?? '',
                'route' => $row->route ?? '',
                'icon' => $row->icon ?? '',
                'name_arr' => $nameData,
                'desc_arr' => $descData,
            ];
        }

        return $returnArr;
    }

    /**
     * Get translation
     *
     * Retrieve a translation value in a specific language.
     */
    private function _getTranslation(string $field = '', int $moduleId = 0, string $table = '', string $column = '', int $languageId = 0): string
    {
        $query = "
        SELECT `i18nTable`.`value`

        FROM `module`
        
        LEFT JOIN `$table` AS `i18nTable` ON (
            `i18nTable`.`$column` = `module`.`$field`
            AND
            `i18nTable`.`language_id` = ?
        )

        WHERE `module`.`module_id` = ?
        ";

        $parameters = [$languageId, $moduleId];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Main query
     *
     * Return the main query used in this model class.
     */
    private function _mainQuery(): ?object
    {
        $query = "
        SELECT
            `module`.`module_id`,
            `module`.`parent_module_id`,
            `module`.`call_sign`,
            `module`.`icon`,
            `module`.`is_active`,
            `module`.`position`,
            `module`.`model_class`,

            `name_i18n`.`value` AS 'name',
            `desc_i18n`.`value` AS 'desc',

            `module__route`.`route`,
            `module__route`.`slug`,
            CONCAT(`module__route`.`slug`, '/') AS 'url'

        FROM `module`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `module`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = `module`.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
        )

        LEFT JOIN `module__route` ON (
            `module__route`.`module_id` = `module`.`module_id`
            AND
            `module__route`.`language_id` = ?
        )
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $this->_languageId
        ];

        return (object) [
            'query' => $query,
            'parameters' => $parameters
        ];
    }

    /**
     * Get all raw
     *
     * Retrieve all module records raw data.
     */
    public function getAllRaw(): array
    {
        $query = $this->_mainQuery()->query;

        $query .= " ORDER BY `module`.`module_id` ";

        $parameters = $this->_mainQuery()->parameters;

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Get by id
     *
     * Retrieve a row using the index column value.
     */
    public function getById(int $moduleId = 0): object|bool
    {
        $query = $this->_mainQuery()->query;

        $query .= " WHERE `module`.`module_id` = ? LIMIT 1 ";

        $parameters = $this->_mainQuery()->parameters;

        $parameters[] = $moduleId;

        $returnArr = $this->fetchObject($query, $parameters);

        // Call the model class counter method
        if (!empty($returnArr->model_class) && method_exists($returnArr->model_class, 'count')) {

            $returnArr->count = (new $returnArr->model_class())->count();
        }

        return $returnArr;
    }

    /**
     * Get by call sign
     *
     * Retrieve a row using the call sign value.
     */
    public function getByCallSign(string $callSign = ''): ?object
    {
        $query = $this->_mainQuery()->query;

        $query .= " WHERE `module`.`call_sign` = ? LIMIT 1 ";

        $parameters = $this->_mainQuery()->parameters;

        $parameters[] = $callSign;

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get by route
     *
     * Retrieve a row using the route value.
     */
    public function getByRoute(string $route = ''): object|bool
    {
        $query = $this->_mainQuery()->query;

        $query .= " WHERE `module__route`.`route` = ? OR `module__route`.`route` = ? LIMIT 1 ";

        $parameters = $this->_mainQuery()->parameters;

        $parameters[] = $route;
        $parameters[] = $route . '/' . APP_DEFAULT_CONTROLLER;

        $returnArr = $this->fetchObject($query, $parameters);

        // Call the model class counter method
        if (!empty($returnArr->model_class) && method_exists($returnArr->model_class, 'count')) {

            $returnArr->count = (new $returnArr->model_class())->count();
        }

        return $returnArr;
    }

    /**
     * Get interfaces for select
     *
     * Select all interface module records for bootstrap select.
     */
    public function getInterfacesForSelect(): array
    {
        $returnArr = [];

        $query = "
        SELECT
            `module`.`module_id`,
            `module`.`call_sign`

        FROM `module`

        WHERE `module`.`parent_module_id` = 0

        ORDER BY `position`
        ";

        $parameters = [];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[$obj->module_id] = $obj->call_sign;

        return $returnArr;
    }

    /**
     * Get all for select
     *
     * Retrieve all modules that are valid parents when moving a module for bootstrap select.
     * This method exclude all child modules and self from the return array.
     */
    public function getAllForSelect(int $moduleId = 0, bool $excludeSelf = false, bool $excludeChild = false): array
    {
        $returnArr = [];
        $childArrFlat = [];

        /*
         * Get child modules
         */
        if ($excludeChild) {

            $childArr = $this->getAllRecursive($moduleId) ?: [];
            $childArrFlat = $this->flattenArray($childArr) ?: [];
        }

        /*
         * Get all modules
         */
        $moduleArr = $this->getAllRaw();

        foreach ($moduleArr as $module) {

            $returnArr[$module->module_id] = $module->route;

            // Exclude self
            if ($excludeSelf && $moduleId == $module->module_id) unset($returnArr[$module->module_id]);

            // Exclude child
            if ($excludeChild && in_array($module->module_id, $childArrFlat)) unset($returnArr[$module->module_id]);
        }

        return $returnArr;
    }

    /**
     * Flatten array
     *
     * Convert a multidimensional array into a single dimensional one.
     */
    public function flattenArray(array $array = []): array
    {
        $returnArr = [];

        if (is_array($array) && count($array) > 0) {

            foreach ($array as $child) {

                $returnArr[] = $child['module_id'];

                if (is_array($child['child'])) {

                    $returnArr = array_merge($returnArr, $this->flattenArray($child['child']));
                }
            }
        }

        return $returnArr;
    }

    /**
     * Get parent module id
     *
     * Retrieve the parent id of a module.
     */
    public function getParentModuleId(int $moduleId = 0): int
    {
        $query = "SELECT `parent_module_id` FROM `module` WHERE `module_id` = ?";

        $parameters = [$moduleId];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Get route data
     *
     * Retrieve the route and slug of a module.
     */
    public function getRouteData(int $moduleId = 0, int $languageId = 0): object|bool
    {
        $query = "SELECT `route`, `slug` FROM `module__route` WHERE `module_id` = ? AND `language_id` = ?";

        $parameters = [$moduleId, $languageId];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get status
     *
     * Retrieve the status of a database row.
     */
    public function getStatus(int $id = 0): int
    {
        $query = "SELECT `is_active` FROM `module` WHERE `module_id` = ?";

        $parameters = [$id];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Set status
     *
     * Update the status of a database row.
     *
     * @throws \Exception
     */
    public function setStatus(int $id = 0, int $newStatus = 0): int
    {
        $query = "UPDATE `module` SET `is_active` = ? WHERE `module_id` = ?";

        $parameters = [$newStatus, $id];

        return $this->update($query, $parameters);
    }

    /**
     * Get position
     *
     * Retrieve the next module position.
     */
    public function getPosition(int $parentId = 0): int
    {
        $query = "SELECT IFNULL(max(`position`) + 1, 1) AS 'position' FROM `module` WHERE `parent_module_id` = ? LIMIT 1";

        $parameters = [$parentId];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Update position
     *
     * Updates the column position in the table `module`.
     *
     * @throws \Exception
     */
    public function updatePosition(int $moduleId = 0, int $position = 0): int
    {
        $query = "UPDATE `module` SET `position` = ? WHERE `module_id` = ?";

        $parameters = [$position, $moduleId];

        return $this->update($query, $parameters);
    }

    /**
     * Update parent id
     *
     * Update the parent id of a module.
     *
     * @throws \Exception
     */
    public function updateParentId(int $moduleId = 0, int $newParentId = 0): int
    {
        $query = "UPDATE `module` SET `parent_module_id` = ? WHERE `module_id` = ?";

        $parameters = [$newParentId, $moduleId];

        return $this->update($query, $parameters);
    }

    /**
     * Add module
     *
     * Inserts a new row into the table `module` and related i18n entries.
     *
     * @throws \Exception
     */
    public function addModule(
        int $parentId = 0,
        string $callSign = '',
        array $nameArr = [],
        array $descArr = [],
        string $icon = '',
        int $isActive = 0,
        int $position = 0
        ): int {

        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);

        // Name
        $sid = $i18nModel->newSid();
        foreach ($nameArr as $languageId => $name) {
            $sidName = $i18nModel->addSmall($name, $languageId, $sid);
        }

        // Desc
        $mid = $i18nModel->newMid();
        foreach ($descArr as $languageId => $desc) {
            $midDesc = $i18nModel->addMedium($desc, $languageId, $mid);
        }

        /*
         * Insert entry
         */
        $query = "
        INSERT INTO `module`(
            `module_id`, `parent_module_id`, `call_sign`, `sid_name`, `mid_desc`, `icon`, `is_active`, `position`, `creation_user_id`
        )
        VALUES (
            NULL, ?, ?, ?, ?, ?, ?, ?, ?
        )
        ";

        $parameters = [
            $parentId,
            $callSign,
            $sidName ?? 0,
            $midDesc ?? 0,
            $icon,
            $isActive,
            $position,
            $this->_userId
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Add route
     *
     * Inserts a new row into the table `module__route`.
     *
     * @throws \Exception
     */
    public function addRoute(int $moduleId = 0, int $languageId = 0, string $route = '', string $slug = ''): int
    {
        $query = "
        INSERT INTO `module__route`(
            `id`, `module_id`, `language_id`, `route`, `slug`
        )
        VALUES (
            NULL, ?, ?, ?, ?
        )
        ";

        $parameters = [
            $moduleId,
            $languageId,
            $route,
            $slug
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit module
     *
     * Updates a row in the table `module` and related i18n entries.
     *
     * @throws \Exception
     */
    public function editModule(
        int $moduleId = 0,
        string $callSign = '',
        array $nameArr = [],
        array $descArr = [],
        string $icon = '',
        int $isActive = 0
        ): int {

        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($moduleId);

        // Name
        foreach ($nameArr as $languageId => $name) {
            $sidName = $i18nModel->editSmall($raw->sid_name, $name, $languageId);
        }

        // Desc
        foreach ($descArr as $languageId => $desc) {
            $midDesc = $i18nModel->editMedium($raw->mid_desc, $desc, $languageId);
        }

        /*
         * Update entry
         */
        $query = "
        UPDATE `module`
        SET
            `call_sign` = ?,
            `sid_name` = ?,
            `mid_desc` = ?,
            `icon` = ?,
            `is_active` = ?,
            `update_user_id` = ?
        WHERE `module_id` = ?
        ";

        $parameters = [
            $callSign,
            $sidName ?? 0,
            $midDesc ?? 0,
            $icon,
            $isActive,
            $this->_userId,
            $moduleId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Edit route
     *
     * Updates a row in the table `module__route`.
     *
     * @throws \Exception
     */
    public function editRoute(int $moduleId = 0, int $languageId = 0, string $route = '', string $slug = ''): int
    {
        $query = "
        UPDATE `module__route`
        SET
            `route` = ?,
            `slug` = ?
        WHERE `module_id` = ?
        AND `language_id` = ?
        ";

        $parameters = [
            $route,
            $slug,
            $moduleId,
            $languageId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Edit child route
     *
     * Updates the route and slug of a module and all its children.
     *
     * @throws \Exception
     */
    public function editChildRoute(int $languageId = 0, string $oldRoute = '', string $newRoute = '', string $oldSlug = '', string $newSlug = ''): int
    {
        $query = "
        UPDATE `module__route`
        SET
            `route` = REPLACE(`route`, '$oldRoute', '$newRoute'),
            `slug` = REPLACE(`slug`, '$oldSlug', '$newSlug')
        WHERE `route` LIKE '$oldRoute%'

        AND `language_id` = ?
        ";

        $parameters = [$languageId];

        return $this->update($query, $parameters);
    }

    /**
     * Edit translation
     *
     * Updates the module translations.
     *
     * @throws \Exception
     */
    public function editTranslation(int $moduleId = 0, array $nameArr = [], array $descArr = []): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($moduleId);

        // Name
        foreach ($nameArr as $languageId => $name) {
            $i18nModel->editSmall($raw->sid_name, $name, $languageId);
        }

        // Desc
        foreach ($descArr as $languageId => $desc) {
            $i18nModel->editMedium($raw->mid_desc, $desc, $languageId);
        }

        return 1;
    }

    /**
     * Del module
     *
     * Delete rows from the table `module`.
     *
     * @throws \Exception
     */
    public function delModule(int $moduleId = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($moduleId);
        $i18nModel->flushSmall($raw->sid_name);
        $i18nModel->flushMedium($raw->mid_desc);

        $query = "DELETE FROM `module` WHERE `module_id` = ?";

        $parameters = [$moduleId];

        return $this->delete($query, $parameters);
    }

    /**
     * Del route
     *
     * Delete rows from the table `module__route`.
     *
     * @throws \Exception
     */
    public function delRoute(int $moduleId = 0): int
    {
        $query = "DELETE FROM `module__route` WHERE `module_id` = ?";

        $parameters = [$moduleId];

        return $this->delete($query, $parameters);
    }

    /**
     * Get raw
     *
     * Retrieve raw data of an existing record.
     */
    public function getRaw(int $id = 0): object|bool
    {
        $query = "SELECT * FROM `module` WHERE `module_id` = ? LIMIT 1";

        $parameters = [$id];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get raw route
     *
     * Retrieve raw data of an existing route record.
     */
    public function getRawRoute(int $id = 0, int $languageId = 0): object|bool
    {
        $query = "SELECT * FROM `module__route` WHERE `module_id` = ? AND `language_id` = ? LIMIT 1";

        $parameters = [$id, $languageId];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get raw route
     *
     * Retrieve raw data of all route records in a given language id.
     */
    public function getAllRawRoute(int $languageId = 0): array
    {
        $query = "SELECT * FROM `module__route` WHERE `language_id` = ?";

        $parameters = [$languageId];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Is duplicated
     *
     * Ascertain if a record exists by searching the value on the field.
     *
     * @throws \Exception
     */
    public function isDuplicated(string $value = '', string $field = '', int $index = 0): string
    {
        $fields = $this->describeTableFields('module__route');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `route` FROM `module__route` WHERE `" . $field . "` = ?";

        $parameters = [$value];

        if ($index) {
            $query .= " AND `module_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
