<?php

namespace Model\Core;

use Lib\MySql;

/**
 * Role
 *
 * Data handler for the MySql table with the same name as this class.
 */
class Role extends MySql
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
     * Get data object
     *
     * Retrieve the data object of a role record.
     */
    public function getDataObject(int $roleId = 0): ?object
    {
        // Load model class
        $languageModel = new \Model\Core\Language();

        // Role object
        $rowObj = $this->getById($roleId);

        /*
         * Translation languages
         */
        $languagesArr = $languageModel->getActive();

        $nameData = [];
        $descData = [];

        foreach ($languagesArr as $language) {

            if ($language->language_id == $this->_languageId) continue;

            $nameData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_name', $roleId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
                'required' => 'required'
            ];

            $descData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('mid_desc', $roleId, 'i18n__text__medium', 'mid', $language->language_id) ?? '',
                'required' => ''
            ];
        }

        /*
         * Return data
         */
        return (object) [
            'role_id' => $rowObj->role_id ?? 0,
            'call_sign' => $rowObj->call_sign ?? '',
            'module_id' => $rowObj->module_id ?? 0,
            'interface' => $rowObj->interface ?? '',
            'data_i18n_id' => $this->_languageId ?? 0,
            'data_i18n_iso2' => $languageModel->getIso2ById($this->_languageId) ?? '',
            'name' => $this->_getTranslation('sid_name', $roleId, 'i18n__text__small', 'sid', $this->_languageId) ?? '',
            'name_i18n' => $nameData,
            'desc' => $this->_getTranslation('mid_desc', $roleId, 'i18n__text__medium', 'mid', $this->_languageId) ?? '',
            'desc_i18n' => $descData
        ];
    }

    /**
     * Get translation
     *
     * Retrieve a translation value in a specific language.
     */
    private function _getTranslation(string $field = '', int $roleId = 0, string $table = '', string $column = '', int $languageId = 0): string
    {
        $query = "
        SELECT `i18nTable`.`value`

        FROM `role`
        
        LEFT JOIN `$table` AS `i18nTable` ON (
            `i18nTable`.`$column` = `role`.`$field`
            AND
            `i18nTable`.`language_id` = ?
        )

        WHERE `role`.`role_id` = ?
        ";

        $parameters = [$languageId, $roleId];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Count
     *
     * Count all available records.
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM `role`";

        return (int) $this->fetchValue($query);
    }

    /**
     * Get all
     *
     * Select all available records.
     */
    public function getAll(int $offset = 0, int $rowCount = 0, string $search = '', string $multiSort = 'role_id DESC', array $multiFilter = []): array
    {
        $query = "
        SELECT
            `role`.`role_id`,
            `role`.`call_sign`,
            `role`.`module_id`,
            `name_i18n`.`value` AS 'name',
            `desc_i18n`.`value` AS 'desc',
            `module`.`call_sign` AS 'interface'

        FROM `role`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `role`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = `role`.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
        )

        LEFT JOIN `module` USING (`module_id`)

        WHERE 1
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId
        ];

        /*
         * Search
         */
        if (!empty($search)) {

            $query .= "
            AND (
                `role`.`role_id` LIKE ?
                OR
                `role`.`call_sign` LIKE ?
                OR
                `name_i18n`.`value` LIKE ?
                OR
                `desc_i18n`.`value` LIKE ?
                OR
                `module`.`call_sign` LIKE ?
            )
            ";

            $parameters[] = '%' . $search . '%';
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
     * Get by id
     *
     * Retrieve a row using the index column value.
     */
    public function getById(int $roleId = 0): object|bool
    {
        $query = "
        SELECT
            `role`.`role_id`,
            `role`.`call_sign`,
            `role`.`module_id`,
            `name_i18n`.`value` AS 'name',
            `desc_i18n`.`value` AS 'desc',
            `module`.`call_sign` AS 'interface'

        FROM `role`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `role`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = `role`.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
        )

        LEFT JOIN `module` USING (`module_id`)

        WHERE `role_id` = ?

        LIMIT 1
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $roleId
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get modules
     *
     * Retrieve an array with all modules and module actions.
     */
    public function getModules(int $roleId = 0, int $parentModuleId = 0): array
    {
        $returnArr = [];

        $moduleArr = (new \Model\Core\Module())->getAllByParentId(0, 0, '', 'position ASC', null, $parentModuleId);

        foreach ($moduleArr as $module) {

            /*
             * Get module actions
             */
            $actionsArr = [];

            $childArr = (new \Model\Core\Module())->getAllByParentId(0, 0, '', 'position ASC', null, $module->module_id);
            $controllerActionArr = (new \Model\Core\ControllerAction())->getAllForSelect();

            foreach ($childArr as $child) {
                if (in_array($child->call_sign, $controllerActionArr)) {
                    $actionsArr[$child->module_id] = $child->call_sign;
                }
            }

            // Go deeper underground
            $child = ($module->child_count > 0) ? $this->getModules($roleId, $module->module_id) : '';

            /*
             * Add to return array
             */
            if (!in_array($module->call_sign, $controllerActionArr)) {

                $returnArr[$module->module_id] = [
                    'module_id' => $module->module_id,
                    'name' => $module->name,
                    'icon' => $module->icon,
                    'child_count' => $module->child_count,
                    'actions' => (!empty($actionsArr)) ? $actionsArr : '',
                    'child' => (!empty($child)) ? $child : ''
                ];
            }
        }

        return $returnArr;
    }

    /**
     * Get all for select
     *
     * Select all available records for bootstrap select.
     */
    public function getAllForSelect(): array
    {
        $returnArr = [];

        $query = "
        SELECT
            `role`.`role_id`,
            `name_i18n`.`value` AS 'name'

        FROM `role`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `role`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        ORDER BY `role_id`
        ";

        $parameters = [$this->_languageId];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[$obj->role_id] = $obj->name;

        return $returnArr;
    }

    /**
     * Add
     *
     * Inserts a new row into the table `role` and related i18n entries.
     *
     * @throws \Exception
     */
    public function add(string $callSign = '', array $nameArr = [], array $descArr = [], int $moduleId = 0): int
    {
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
        INSERT INTO `role`(
            `role_id`, `call_sign`, `sid_name`, `mid_desc`, `module_id`
        )
        VALUES (
            NULL, ?, ?, ?, ?
        )
        ";

        $parameters = [
            $callSign,
            $sidName ?? 0,
            $midDesc ?? 0,
            $moduleId,
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit
     *
     * Updates a row in the table `role` and related i18n entries.
     *
     * @throws \Exception
     */
    public function edit(int $roleId = 0, string $callSign = '', array $nameArr = [], array $descArr = [], int $moduleId = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($roleId);

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
        UPDATE `role` 
        SET 
            `call_sign` = ?,
            `sid_name` = ?,
            `mid_desc` = ?,
            `module_id` = ?
        WHERE `role_id` = ?
        ";

        $parameters = [
            $callSign,
            $sidName ?? 0,
            $midDesc ?? 0,
            $moduleId,
            $roleId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Del
     *
     * Delete a row from the table `role` and related i18n entries.
     *
     * @throws \Exception
     */
    public function del(int $roleId = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($roleId);
        $i18nModel->flushSmall($raw->sid_name);
        $i18nModel->flushMedium($raw->mid_desc);

        $query = "DELETE FROM `role` WHERE `role_id` = ?";

        $parameters = [$roleId];

        return $this->delete($query, $parameters);
    }

    /**
     * Get role permission
     *
     * Retrieves the registered role permissions.
     */
    public function getRolePermission(int $roleId = 0): array
    {
        $returnArr = [];

        $query = "SELECT * FROM `role__permission` WHERE `role_id` = ?";

        $parameters = [$roleId];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[] = $obj->module_id;

        return $returnArr;
    }

    /**
     * Add permission
     *
     * Inserts a new role permission into the table `role__permission`.
     *
     * @throws \Exception
     */
    public function addPermission(int $roleId = 0, int $moduleId = 0): int
    {
        $query = "
        INSERT INTO `role__permission`
            (`role_permission_id`, `role_id`, `module_id`)
        VALUES
            (NULL, ?, ?)
        ";

        $parameters = [$roleId, $moduleId];

        return $this->insert($query, $parameters);
    }

    /**
     * Flush permission
     *
     * Deletes all role permissions from the table `role__permission`.
     *
     * @throws \Exception
     */
    public function flushPermission(int $roleId = 0): int
    {
        $query = "DELETE FROM `role__permission` WHERE `role_id` = ?";

        $parameters = [$roleId];

        return $this->delete($query, $parameters);
    }

    /**
     * Get raw
     *
     * Retrieve raw data of an existing record.
     */
    public function getRaw(int $id = 0): object|bool
    {
        $query = "SELECT * FROM `role` WHERE `role_id` = ? LIMIT 1";

        $parameters = [$id];

        return $this->fetchObject($query, $parameters);
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
        $fields = $this->describeTableFields('role');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `call_sign` FROM `role` WHERE `" . $field . "` = ?";

        $parameters = [$value];

        if ($index) {
            $query .= " AND `role_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
