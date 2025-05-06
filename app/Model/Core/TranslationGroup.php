<?php

namespace Model\Core;

use Lib\MySql;

/**
 * Translation group
 *
 * Data handler for the MySql table with the same name as this class.
 */
class TranslationGroup extends MySql
{
    private string $_dbConn;
    private int $translationId;

    /**
     * Class constructor
     *
     * @throws \Exception
     */
    public function __construct(int $translationId = 0)
    {
        // Set database connector
        $this->_dbConn = 'core';

        // Set translation id
        $this->translationId = $translationId;

        // Initialize database connector
        parent::__construct($this->_dbConn);
    }

    /**
     * Get translation group data
     *
     * Get the necessary data to populate the translation group formulary.
     */
    public function getGroupData(int $groupId = 0): ?object
    {
        $languagesData = [];

        // Group row
        $rowObj = $this->getById($groupId);

        /*
         * Group languages
         */
        $languagesArr = (new \Model\Core\Language())->getActive();

        foreach ($languagesArr as $language) {

            $languagesData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getValue($groupId, $language->language_id) ?? '',
            ];
        }

        /*
         * Return data
         */
        return (object) [
            'translation_group_id' => $rowObj->translation_group_id ?? '',
            'call_sign' => $rowObj->call_sign ?? '',
            'translations' => $languagesData
        ];
    }

    /**
     * Get value
     *
     * Retrieve a translation group value in a specific language.
     */
    private function _getValue(int $groupId = 0, int $languageId = 0): string
    {
        $query = "
        SELECT `i18nTable`.`value`

        FROM `translation__group`
        
        LEFT JOIN `i18n__text__small` AS `i18nTable` ON (
            `i18nTable`.`sid` = `translation__group`.`sid_name`
            AND
            `i18nTable`.`language_id` = ?
        )

        WHERE `translation__group`.`translation_group_id` = ?
        ";

        $parameters = [$languageId, $groupId];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Get all
     *
     * Select all available records.
     */
    public function getAll(int $offset = 0, int $rowCount = 0, string $search = '', string $multiSort = 'translation_group_id DESC', array $multiFilter = []): array
    {
        $query = "
        SELECT 
            `translation__group`.`translation_group_id`, 
            `translation__group`.`translation_id`, 
            `translation__group`.`call_sign`, 

            `name_i18n`.`value` AS 'value',
            `name_i18n`.`slug` AS 'slug',

            (
                SELECT COUNT(*)
                FROM `translation__item`
                WHERE `translation__item`.`translation_id` = `translation__group`.`translation_id`
                AND `translation__item`.`translation_group_id` = `translation__group`.`translation_group_id`
            ) AS 'item_count'
        
        FROM `translation__group`
        
        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `translation__group`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )
        
        WHERE `translation__group`.`translation_id` = ?
        ";

        $parameters = [
            $this->_languageId,
            $this->translationId
        ];

        /*
         * Search
         */
        if (!empty($search)) {

            $query .= "
            AND (
                `translation__group`.`translation_id` LIKE ?
                OR
                `translation__group`.`translation_group_id` LIKE ?
                OR
                `translation__group`.`call_sign` LIKE ?
                OR
                `name_i18n`.`value` LIKE ?
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
    public function getById(int $translationGroupId = 0): object|bool
    {
        $query = "
        SELECT 
            `translation__group`.`translation_group_id`,     
            `translation__group`.`translation_id`, 
            `translation__group`.`call_sign`, 
            `translation__group`.`sid_name`, 

            `name_i18n`.`value` AS value,
            `name_i18n`.`slug` AS slug,

            (
                SELECT COUNT(*)
                FROM `translation__item`
                WHERE `translation__item`.`translation_id` = `translation__group`.`translation_id`
                AND `translation__item`.`translation_group_id` = `translation__group`.`translation_group_id`
            ) AS 'item_count'
        
        FROM `translation__group`
        
        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `translation__group`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )
        
        WHERE `translation__group`.`translation_group_id` =?
        AND `translation__group`.`translation_id` =?

        LIMIT 1
        ";

        $parameters = [
            $this->_languageId,
            $translationGroupId,
            $this->translationId
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get group items
     *
     * Get all items associated with the given group id.
     */
    public function getGroupItems(int $groupId = 0): array
    {
        $query = "SELECT `translation_item_id` FROM `translation__item` WHERE `translation_group_id` = ? ORDER BY `translation_item_id`";

        $parameters = [$groupId];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Add
     *
     * Inserts a new row into the table `translation__group` and related i18n entries.
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
        INSERT INTO `translation__group`(
            `translation_group_id`, `translation_id`, `call_sign`, `sid_name`, `creation_user_id`
        )
        VALUES (
            NULL, ?, ?, ?, ?
        )
        ";

        $parameters = [
            $this->translationId,
            $callSign,
            $sidName ?? 0,
            $this->_userId
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit
     *
     * Updates a row in the table `translation__group` and related i18n entries.
     *
     * @throws \Exception
     */
    public function edit(int $groupId = 0, string $callSign = '', array $valueArr = []): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($groupId);

        foreach ($valueArr as $languageId => $value) {
            $sidName = $i18nModel->editSmall($raw->sid_name, $value, $languageId);
        }

        /*
         * Update entry
         */
        $query = "
        UPDATE `translation__group` 
        SET 
            `call_sign` = ?,
            `sid_name` = ?, 
            `update_user_id` = ?
        WHERE `translation_group_id` = ?
        AND `translation_id` = ?
        ";

        $parameters = [
            $callSign,
            $sidName ?? 0,
            $this->_userId,
            $groupId,
            $this->translationId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Delete
     *
     * Delete rows from the table `translation__group` and related i18n entries.
     *
     * @throws \Exception
     */
    public function del(int $groupId = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($groupId);
        $i18nModel->flushSmall($raw->sid_name);

        $query = "DELETE FROM `translation__group` WHERE `translation_group_id` = ? AND `translation_id` = ?";

        $parameters = [$groupId, $this->translationId];

        return $this->delete($query, $parameters);
    }

    /**
     * Get raw
     *
     * Retrieve raw data of an existing record.
     */
    public function getRaw(int $id = 0): object|bool
    {
        $query = "SELECT * FROM `translation__group` WHERE `translation_group_id` = ? AND `translation_id` = ? LIMIT 1";

        $parameters = [$id, $this->translationId];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Is duplicated
     *
     * Ascertain if a record exists by searching the value on the field.
     *
     * @throws \Exception
     */
    public function isDuplicated(string $value = '', string $field = '', int $translationId = 0, int $index = 0): string
    {
        $fields = $this->describeTableFields('translation__group');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `call_sign` FROM `translation__group` WHERE `translation_id` = ? AND `" . $field . "` = ?";

        $parameters = [$translationId, $value];

        if ($index) {
            $query .= " AND `translation_group_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
