<?php

namespace Model\Core;

use Lib\MySql;

/**
 * Translation
 *
 * Data handler for the MySql table with the same name as this class.
 */
class Translation extends MySql
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
        $query = "SELECT COUNT(*) as total FROM `translation`";

        return (int) $this->fetchValue($query);
    }

    /**
     * Get translation data
     *
     * Get the necessary data to populate the translation formulary.
     */
    public function getTranslationData(int $translationId = 0): ?object
    {
        $languagesData = [];

        // Translation row
        $rowObj = $this->getById($translationId);

        /*
         * Translation languages
         */
        $languagesArr = (new \Model\Core\Language())->getActive();

        foreach ($languagesArr as $language) {

            $languagesData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getValue($translationId, $language->language_id) ?? '',
            ];
        }

        /*
         * Return data
         */
        return (object) [
            'translation_id' => $rowObj->translation_id ?? '',
            'call_sign' => $rowObj->call_sign ?? '',
            'translations' => $languagesData
        ];
    }

    /**
     * Get value
     *
     * Retrieve a translation value in a specific language.
     */
    private function _getValue(int $translationId = 0, int $languageId = 0): string
    {
        $query = "
        SELECT `i18nTable`.`value`

        FROM `translation`
        
        LEFT JOIN `i18n__text__small` AS `i18nTable` ON (
            `i18nTable`.`sid` = `translation`.`sid_name`
            AND
            `i18nTable`.`language_id` = ?
        )

        WHERE `translation`.`translation_id` = ?
        ";

        $parameters = [$languageId, $translationId];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Get all
     *
     * Select all available records.
     */
    public function getAll(int $offset = 0, int $rowCount = 0, string $search = '', string $multiSort = 'translation_id DESC', array $multiFilter = []): array
    {
        $query = "
        SELECT 
            `translation`.`translation_id`, 
            `translation`.`call_sign`, 

            `name_i18n`.`value` AS 'value',
            `name_i18n`.`slug` AS 'slug',

            (
                SELECT COUNT(*)
                FROM `translation__group`
                WHERE `translation__group`.`translation_id` = `translation`.`translation_id`
            ) AS 'group_count'
        
        FROM `translation`
        
        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `translation`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )
        
        WHERE 1
        ";

        $parameters = [
            $this->_languageId
        ];

        /*
         * Search
         */
        if (!empty($search)) {

            $query .= "
            AND (
                `translation`.`translation_id` LIKE ?
                OR
                `translation`.`call_sign` LIKE ?
                OR
                `name_i18n`.`value` LIKE ?
            )
            ";

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
    public function getById(int $translationId = 0): object|bool
    {
        $query = "
        SELECT 
            `translation`.`translation_id`, 
            `translation`.`call_sign`, 

            `name_i18n`.`value` AS 'value',
            `name_i18n`.`slug` AS 'slug',

            `translation`.`creation_date`,
            `translation`.`creation_user_id`,
            `translation`.`update_date`,
            `translation`.`update_user_id`,

            (
                SELECT COUNT(*)
                FROM `translation__group`
                WHERE `translation__group`.`translation_id` = `translation`.`translation_id`
            ) AS 'group_count'
        
        FROM `translation`
        
        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `translation`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )
        
        WHERE `translation`.`translation_id` =?

        LIMIT 1
        ";

        $parameters = [
            $this->_languageId,
            $translationId
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get by call sign
     *
     * Retrieves a row using the call sign.
     */
    public function getByCallSign(string $callSign = ''): object|bool
    {
        $query = "
        SELECT 

            `translation`.`translation_id`, 
            `translation`.`call_sign`, 
            `translation`.`sid_name`, 

            `i18n__text__small`.`value` AS value,
            `i18n__text__small`.`slug` AS slug
        
        FROM `translation`
        
        INNER JOIN `i18n__text__small` ON (
            `i18n__text__small`.`sid` = `translation`.`sid_name`
            AND 
            `i18n__text__small`.`language_id` =?
        )
        
        WHERE `translation`.`call_sign` =?

        LIMIT 1
        ";

        $parameters = [
            $this->_languageId,
            $callSign
        ];

        return $this->fetchObject($query, $parameters);
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
            `translation`.`translation_id`, 
            `name_i18n`.`value` AS 'name'

        FROM `translation`
        
        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `translation`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        ORDER BY `translation_id`
        ";

        $parameters = [$this->_languageId];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[$obj->translation_id] = $obj->name;

        return $returnArr;
    }

    /**
     * Get translation groups
     *
     * Get all groups associated with the given translation id.
     */
    public function getTranslationGroups(int $translationId = 0): array
    {
        $query = "SELECT `translation_group_id` FROM `translation__group` WHERE `translation_id` = ? ORDER BY `translation_group_id`";

        $parameters = [$translationId];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Get translation groups call signs
     *
     * Get all groups call signs associated with the given translation id.
     */
    public function getTranslationGroupsCallSigns(int $translationId = 0): array
    {
        $returnArr = [];

        $query = "SELECT `call_sign` FROM `translation__group` WHERE `translation_id` = ?";

        $parameters = [$translationId];

        $arr =  $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[] = $obj->call_sign;

        return $returnArr;
    }

    /**
     * Add
     *
     * Inserts a new row into the table `translation` and related i18n entries.
     *
     * @throws \Exception
     */
    public function add(string $callSign = '', array $valueArr = []): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $sid = $i18nModel->newSid();

        foreach ($valueArr as $languageId => $value) {
            $sidName = $i18nModel->addSmall($value, $languageId, $sid);
        }

        /*
         * Insert entry
         */
        $query = "
        INSERT INTO `translation`(
            `translation_id`, `call_sign`, `sid_name`, `creation_user_id`
        )
        VALUES (
            NULL, ?, ?, ?
        )
        ";

        $parameters = [
            $callSign,
            $sidName ?? 0,
            $this->_userId
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit
     *
     * Updates a row in the table `translation` and related i18n entries.
     *
     * @throws \Exception
     */
    public function edit(int $translationId = 0, string $callSign = '', array $valueArr = []): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($translationId);

        foreach ($valueArr as $languageId => $value) {
            $sidName = $i18nModel->editSmall($raw->sid_name, $value, $languageId);
        }

        /*
         * Update entry
         */
        $query = "
        UPDATE `translation` 
        SET 
            `call_sign` = ?,
            `sid_name` = ?, 
            `update_user_id` = ?
        WHERE  `translation_id` = ?
        ";

        $parameters = [
            $callSign,
            $sidName ?? 0,
            $this->_userId,
            $translationId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Delete
     *
     * Delete rows from the table `translation` and related i18n entries.
     *
     * @throws \Exception
     */
    public function del(int $translationId = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($translationId);
        $i18nModel->flushSmall($raw->sid_name);

        $query = "DELETE FROM `translation` WHERE `translation_id` = ?";

        $parameters = [$translationId];

        return $this->delete($query, $parameters);
    }

    /**
     * Get raw
     *
     * Retrieve raw data of an existing record.
     */
    public function getRaw(int $id = 0): object|bool
    {
        $query = "SELECT * FROM `translation` WHERE `translation_id` = ? LIMIT 1";

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
        $fields = $this->describeTableFields('translation');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `call_sign` FROM `translation` WHERE `" . $field . "` = ?";

        $parameters = [$value];

        if ($index) {
            $query .= " AND `translation_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
