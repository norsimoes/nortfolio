<?php

namespace Model\Core;

use Lib\MySql;

/**
 * Blank
 *
 * Data handler for the MySql table with the same name as this class.
 */
class Blank extends MySql
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
     * Retrieve the data object of a blank record.
     */
    public function getDataObject(int $blankId = 0): object|bool
    {
        /*
         * Load classes
         */
        $languageModel = new \Model\Core\Language();

        /*
         * Blank row
         */
        $rowObj = $this->getById($blankId);

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
                'value' => $this->_getTranslation('sid_name', $blankId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $descData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('mid_desc', $blankId, 'i18n__text__medium', 'mid', $language->language_id) ?? '',
            ];
        }

        /*
         * Return data
         */
        return (object) [
            'blank_id' => $rowObj->blank_id ?? 0,
            'call_sign' => $rowObj->call_sign ?? '',
            'position' => $rowObj->position ?? '',
            'data_i18n_id' => $this->_languageId ?? 0,
            'data_i18n_iso2' => $languageModel->getIso2ById($this->_languageId) ?? '',
            'name' => $rowObj->name ?? '',
            'name_i18n' => $nameData,
            'description' => $rowObj->description ?? '',
            'description_i18n' => $descData
        ];
    }

    /**
     * Get translation
     *
     * Retrieve a translation value in a specific language.
     */
    private function _getTranslation(string $field = '', int $blankId = 0, string $table = '', string $column = '', int $languageId = 0): string
    {
        $query = "
        SELECT `i18nTable`.`value`

        FROM `blank`
        
        LEFT JOIN `$table` AS `i18nTable` ON (
            `i18nTable`.`$column` = `blank`.`$field`
            AND
            `i18nTable`.`language_id` = ?
        )

        WHERE `blank`.`blank_id` = ?
        ";

        $parameters = [$languageId, $blankId];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Count
     *
     * Count all available records.
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM `blank`";

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
            `blank`.`blank_id`,
            `blank`.`call_sign`,
            `blank`.`position`,
            `name_i18n`.`value` AS 'name',
            `desc_i18n`.`value` AS 'description'

        FROM `blank`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `blank`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = `blank`.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
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
                `blank`.`blank_id` LIKE ?
                OR 
                `blank`.`call_sign` LIKE ?
                OR 
                `name_i18n`.`value` LIKE ?
                OR 
                `desc_i18n`.`value` LIKE ?
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
    public function getById(int $blankId = 0): object|bool
    {
        $query = "
        SELECT
            `blank`.`blank_id`,
            `blank`.`call_sign`,
            `blank`.`position`,
            `name_i18n`.`value` AS 'name',
            `desc_i18n`.`value` AS 'description'

        FROM `blank`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `blank`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = `blank`.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
        )

        WHERE `blank`.`blank_id` = ?

        LIMIT 1
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $blankId
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get all for select
     *
     * Select all available records for bootstrap select.
     */
    public function getAllForSelect(string $order = 'blank_id ASC'): array
    {
        $returnArr = [];

        $query = "
        SELECT
            `blank`.`blank_id`,
            `name_i18n`.`value` AS 'name'

        FROM `blank`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `blank`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        ORDER BY " . $order . "
        ";

        $parameters = [$this->_languageId];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[$obj->blank_id] = $obj->name;

        return $returnArr;
    }

    /**
     * Get all for filter
     *
     * Select all available records for filter dropdown.
     */
    public function getAllForFilter(string $order = 'blank_id ASC'): array
    {
        $query = "
        SELECT
            `blank`.`blank_id`,
            `name_i18n`.`value` AS 'name'

        FROM `blank`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `blank`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        ORDER BY " . $order ."
        ";

        $parameters = [$this->_languageId];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Get position
     *
     * Retrieve the next record position.
     */
    public function getPosition(): int
    {
        $query = "SELECT IFNULL(max(`position`) + 1, 1) AS 'position' FROM `blank` LIMIT 1";

        $parameters = [];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Update position
     *
     * Updates the column position in the table `blank`.
     *
     * @throws \Exception
     */
    public function updatePosition(int $blankId = 0, int $position = 0): int
    {
        $query = "UPDATE `blank` SET `position` = ? WHERE `blank_id` = ?";

        $parameters = [$position, $blankId];

        return $this->update($query, $parameters);
    }

    /**
     * Add
     *
     * Adds a new row to the table `blank` and related i18n entries.
     *
     * @throws \Exception
     */
    public function add(string $callSign = '', array $nameArr = [], array $descArr = [], int $position = 0): int
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

        // Description
        $mid = $i18nModel->newMid();
        foreach ($descArr as $languageId => $desc) {
            $midDesc = $i18nModel->addMedium($desc, $languageId, $mid);
        }

        $query = "
        INSERT INTO `blank` 
            (`blank_id`, `call_sign`, `sid_name`, `mid_desc`, `position`, `creation_user_id`)
        VALUES 
            (NULL, ?, ?, ?, ?, ?)
        ";

        $parameters = [
            $callSign,
            $sidName ?? 0,
            $midDesc ?? 0,
            $position,
            $this->_userId
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit
     *
     * Updates a row in the table `blank` and related i18n entries.
     *
     * @throws \Exception
     */
    public function edit(int $blankId = 0, string $callSign = '', array $nameArr = [], array $descArr = []): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($blankId);

        // Name
        foreach ($nameArr as $languageId => $name) {
            $sidName = $i18nModel->editSmall($raw->sid_name, $name, $languageId);
        }

        // Description
        foreach ($descArr as $languageId => $desc) {
            $midDesc = $i18nModel->editMedium($raw->mid_desc, $desc, $languageId);
        }

        $query = "
        UPDATE `blank` 
        SET 
            `call_sign` = ?,
            `sid_name` = ?,
            `mid_desc` = ?,
            `update_user_id` = ?
        WHERE `blank_id` = ?
        ";

        $parameters = [
            $callSign,
            $sidName ?? 0,
            $midDesc ?? 0,
            $this->_userId,
            $blankId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Del
     *
     * Delete a row from the table `blank` and related i18n entries.
     *
     * @throws \Exception
     */
    public function del(int $blankId = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($blankId);

        $i18nModel->flushSmall($raw->sid_name);
        $i18nModel->flushMedium($raw->mid_desc);

        $query = "DELETE FROM `blank` WHERE `blank_id` = ?";

        $parameters = [$blankId];

        return $this->delete($query, $parameters);
    }

    /**
     * Get raw
     *
     * Retrieve raw data of an existing record.
     */
    public function getRaw(int $id = 0): object|bool
    {
        $query = "SELECT * FROM `blank` WHERE `blank_id` = ? LIMIT 1";

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
        $fields = $this->describeTableFields('blank');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `blank_id` FROM `blank` WHERE `" . $field . "` = ?";

        $parameters = [$value];

        if ($index) {
            $query .= " AND `blank_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
