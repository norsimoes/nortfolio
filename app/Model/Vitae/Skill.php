<?php

namespace Model\Vitae;

use Lib\MySql;

/**
 * Skill
 *
 * Data handler for the MySql table with the same name as this class.
 */
class Skill extends MySql
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
        $this->_dbConn = 'vitae';

        // Initialize database connector
        parent::__construct($this->_dbConn);
    }

    /**
     * Get data object
     *
     * Retrieve the data object of a skill record.
     */
    public function getDataObject(int $skillId = 0): object|bool
    {
        /*
         * Load classes
         */
        $languageModel = new \Model\Core\Language();

        /*
         * Skill row
         */
        $rowObj = $this->getById($skillId);

        /*
         * Translation languages
         */
        $languagesArr = $languageModel->getActive();

        $nameData = [];
        $overrideData = [];

        foreach ($languagesArr as $language) {

            if ($language->language_id == $this->_languageId) continue;

            $nameData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_name', $skillId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $overrideData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_override', $skillId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];
        }

        /*
         * Return data
         */
        return (object) [
            'skill_id' => $rowObj->skill_id ?? 0,
            'type' => $rowObj->type ?? '',
            'value' => $rowObj->value ?? 0,
            'icon' => $rowObj->icon ?? '',
            'position' => $rowObj->position ?? '',
            'data_i18n_id' => $this->_languageId ?? 0,
            'data_i18n_iso2' => $languageModel->getIso2ById($this->_languageId) ?? '',
            'name' => $rowObj->name ?? '',
            'name_i18n' => $nameData,
            'override' => $rowObj->override ?? '',
            'override_i18n' => $overrideData,
        ];
    }

    /**
     * Get translation
     *
     * Retrieve a translation value in a specific language.
     */
    private function _getTranslation(string $field = '', int $skillId = 0, string $table = '', string $column = '', int $languageId = 0): ?string
    {
        $query = "
        SELECT `i18nTable`.`value`

        FROM `skill`
        
        LEFT JOIN `$table` AS `i18nTable` ON (
            `i18nTable`.`$column` = `skill`.`$field`
            AND
            `i18nTable`.`language_id` = ?
        )

        WHERE `skill`.`skill_id` = ?
        ";

        $parameters = [$languageId, $skillId];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Count
     *
     * Count all available records.
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM `skill`";

        return (int) $this->fetchValue($query);
    }

    /**
     * Get all
     *
     * Select all available records.
     */
    public function getAll(int $offset = 0, int $rowCount = 0, string $search = '', string $multiSort = 'position ASC', array $multiFilter = []): array
    {
        $query = "
        SELECT
            `skill`.`skill_id`,
            `skill`.`type`,
            `skill`.`value`,
            `skill`.`icon`,
            `skill`.`position`,
            `name_i18n`.`value` AS 'name',
            `override_i18n`.`value` AS 'override'

        FROM `skill`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `skill`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `override_i18n` ON (
            `override_i18n`.`sid` = `skill`.`sid_override`
            AND
            `override_i18n`.`language_id` = ?
        )

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
                `skill`.`skill_id` LIKE ?
                OR 
                `skill`.`type` LIKE ?
                OR 
                `name_i18n`.`value` LIKE ?
                OR 
                `override_i18n`.`value` LIKE ?
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
     * Get by id
     *
     * Retrieve a row using the index column value.
     */
    public function getById(int $skillId = 0): object|bool
    {
        $query = "
        SELECT
            `skill`.`skill_id`,
            `skill`.`type`,
            `skill`.`value`,
            `skill`.`icon`,
            `skill`.`position`,
            `name_i18n`.`value` AS 'name',
            `override_i18n`.`value` AS 'override'

        FROM `skill`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `skill`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `override_i18n` ON (
            `override_i18n`.`sid` = `skill`.`sid_override`
            AND
            `override_i18n`.`language_id` = ?
        )

        WHERE `skill`.`skill_id` = ?

        LIMIT 1
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $skillId
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get by type
     *
     * Retrieve a row using the index column value.
     */
    public function getByType(string $type = ''): array
    {
        $query = "
        SELECT
            `name_i18n`.`value` AS 'name',
            `override_i18n`.`value` AS 'override',
            `skill`.`value`,
            `skill`.`icon`

        FROM `skill`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `skill`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `override_i18n` ON (
            `override_i18n`.`sid` = `skill`.`sid_override`
            AND
            `override_i18n`.`language_id` = ?
        )

        WHERE `skill`.`type` = ?

        ORDER BY `skill`.`position`
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $type
        ];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Get all for select
     *
     * Select all available records for bootstrap select.
     */
    public function getAllForSelect(string $order = 'skill_id ASC'): array
    {
        $returnArr = [];

        $query = "
        SELECT
            `skill`.`skill_id`,
            `name_i18n`.`value` AS 'name'

        FROM `skill`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `skill`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        ORDER BY " . $order . "
        ";

        $parameters = [$this->_languageId];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[$obj->skill_id] = $obj->name;

        return $returnArr;
    }

    /**
     * Get position
     *
     * Retrieve the next record position.
     */
    public function getPosition(): int
    {
        $query = "SELECT IFNULL(max(`position`) + 1, 1) AS 'position' FROM `skill` LIMIT 1";

        $parameters = [];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Update position
     *
     * Updates the column position in the table `skill`.
     *
     * @throws \Exception
     */
    public function updatePosition(int $skillId = 0, int $position = 0): int
    {
        $query = "UPDATE `skill` SET `position` = ? WHERE `skill_id` = ?";

        $parameters = [$position, $skillId];

        return $this->update($query, $parameters);
    }

    /**
     * Add
     *
     * Adds a new row to the table `skill` and related i18n entries.
     *
     * @throws \Exception
     */
    public function add(array $nameArr = [], array $overrideArr = [], string $type = '', int $value = 0, string $icon = '', int $position = 0): int
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

        // Override
        $sid = $i18nModel->newSid();
        foreach ($overrideArr as $languageId => $override) {
            $sidOverride = $i18nModel->addSmall($override, $languageId, $sid);
        }

        $query = "
        INSERT INTO `skill` 
            (`skill_id`, `sid_name`, `sid_override`, `type`, `value`, `icon`, `position`, `creation_user_id`)
        VALUES 
            (NULL, ?, ?, ?, ?, ?, ?, ?)
        ";

        $parameters = [
            $sidName ?? 0,
            $sidOverride ?? 0,
            $type,
            $value,
            $icon,
            $position,
            $this->_userId
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit
     *
     * Updates a row in the table `skill` and related i18n entries.
     *
     * @throws \Exception
     */
    public function edit(int $skillId = 0, array $nameArr = [], array $overrideArr = [], string $type = '', int $value = 0, string $icon = ''): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($skillId);

        // Name
        foreach ($nameArr as $languageId => $name) {
            $sidName = $i18nModel->editSmall($raw->sid_name, $name, $languageId);
        }

        // Override
        foreach ($overrideArr as $languageId => $override) {
            $sidOverride = $i18nModel->editSmall($raw->sid_override, $override, $languageId);
        }

        $query = "
        UPDATE `skill` 
        SET 
            `sid_name` = ?,
            `sid_override` = ?,
            `type` = ?,
            `value` = ?,
            `icon` = ?,
            `update_user_id` = ?
        WHERE `skill_id` = ?
        ";

        $parameters = [
            $sidName ?? 0,
            $sidOverride ?? 0,
            $type,
            $value,
            $icon,
            $this->_userId,
            $skillId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Del
     *
     * Delete a row from the table `skill` and related i18n entries.
     *
     * @throws \Exception
     */
    public function del(int $skillId = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($skillId);
        $i18nModel->flushSmall($raw->sid_name);
        $i18nModel->flushSmall($raw->sid_override);

        $query = "DELETE FROM `skill` WHERE `skill_id` = ?";

        $parameters = [$skillId];

        return $this->delete($query, $parameters);
    }

    /**
     * Del icon
     *
     * Clear the column `icon` of a row in the table `skill`.
     *
     * @throws \Exception
     */
    public function delIcon(int $skillId = 0): int
    {
        $query = "UPDATE `skill` SET `icon` = '' WHERE `skill_id` = ?";

        $parameters = [$skillId];

        return $this->update($query, $parameters);
    }

    /**
     * Get raw
     *
     * Retrieve raw data of an existing record.
     */
    public function getRaw(int $id = 0): object|bool
    {
        $query = "SELECT * FROM `skill` WHERE `skill_id` = ? LIMIT 1";

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
        $fields = $this->describeTableFields('skill');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `skill_id` FROM `skill` WHERE `" . $field . "` = ?";

        $parameters = [$value];

        if ($index) {
            $query .= " AND `skill_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
