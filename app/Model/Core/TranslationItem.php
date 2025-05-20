<?php

namespace Model\Core;

use Lib\MySql;

/**
 * Translation item
 *
 * Data handler for the MySql table with the same name as this class.
 */
class TranslationItem extends MySql
{
    private string $_dbConn;
    private int $translationId;
    private int $translationGroupId;

    /**
     * Class constructor
     *
     * @throws \Exception
     */
    public function __construct(int $translationId = 0, int $translationGroupId = 0)
    {
        // Set database connector
        $this->_dbConn = 'core';

        // Set translation and group ids
        $this->translationId = $translationId;
        $this->translationGroupId = $translationGroupId;

        // Initialize database connector
        parent::__construct($this->_dbConn);
    }

    /**
     * Get translation item data
     *
     * Get the necessary data to populate the translation item formulary.
     */
    public function getItemData(int $itemId = 0): ?object
    {
        $languagesData = [];

        // Item row
        $rowObj = $this->getById($itemId);

        /*
         * Item languages
         */
        $languagesArr = (new \Model\Core\Language())->getActive();

        foreach ($languagesArr as $language) {

            $languagesData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getValue($itemId, $language->language_id) ?? '',
            ];
        }

        /*
         * Return data
         */
        return (object) [
            'translation_item_id' => $rowObj->translation_item_id ?? '',
            'array_key' => $rowObj->array_key ?? '',
            'translations' => $languagesData
        ];
    }

    /**
     * Get value
     *
     * Retrieve a translation item value in a specific language.
     */
    private function _getValue(int $itemId = 0, int $languageId = 0): string
    {
        $query = "
        SELECT `i18nTable`.`value`

        FROM `translation__item`
        
        LEFT JOIN `i18n__text__small` AS `i18nTable` ON (
            `i18nTable`.`sid` = `translation__item`.`sid_name`
            AND
            `i18nTable`.`language_id` = ?
        )

        WHERE `translation__item`.`translation_item_id` = ?
        ";

        $parameters = [$languageId, $itemId];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Get all
     *
     * Select all available records.
     */
    public function getAll(int $offset = 0, int $rowCount = 0, string $search = '', string $multiSort = 'translation_item_id ASC', array $multiFilter = []): array
    {
        $query = "
        SELECT 
            `translation__item`.`translation_item_id`, 
            `translation__item`.`translation_group_id`, 
            `translation__item`.`translation_id`,
            `translation__item`.`array_key`,

            `name_i18n`.`value` AS 'value',
            `name_i18n`.`slug` AS 'slug'
        
        FROM `translation__item`
        
        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `translation__item`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )
        
        WHERE `translation__item`.`translation_id` = ?
        AND `translation__item`.`translation_group_id` = ?
        ";

        $parameters = [
            $this->_languageId,
            $this->translationId,
            $this->translationGroupId
        ];

        /*
         * Search
         */
        if (!empty($search)) {

            $query .= "
            AND (
                `translation__item`.`translation_id` LIKE ?
                OR
                `translation__item`.`translation_group_id` LIKE ?
                OR
                `translation__item`.`array_key` LIKE ?
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
    public function getById(int $translationItemId = 0): object|bool
    {
        $query = "
        SELECT 
            `translation__item`.`translation_item_id`,     
            `translation__item`.`translation_id`, 
            `translation__item`.`translation_group_id`, 
            `translation__item`.`array_key`, 
            `translation__item`.`sid_name`, 
            `i18n__text__small`.`value` AS value,
            `i18n__text__small`.`slug` AS slug
        
        FROM `translation__item`
        
        INNER JOIN `i18n__text__small` ON (
            `i18n__text__small`.`sid` = `translation__item`.`sid_name`
            AND 
            `i18n__text__small`.`language_id` =?
        )

        WHERE 
            `translation__item`.`translation_item_id` =?
            AND
            `translation__item`.`translation_id` =?
            AND
            `translation__item`.`translation_group_id` =?

        LIMIT 1
        ";

        $parameters = [
            $this->_languageId,
            $translationItemId,
            $this->translationId,
            $this->translationGroupId
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get next array key
     *
     * Retrieve the next array key position.
     */
    public function getNextArrayKey(): int
    {
        $query = "
        SELECT IFNULL(max(`array_key`) + 1, 1) AS 'position' 
        FROM `translation__item` 
        WHERE `translation_id` = ? 
        AND `translation_group_id` = ? 
        LIMIT 1
        ";

        $parameters = [
            $this->translationId,
            $this->translationGroupId
        ];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Add
     *
     * Inserts a new row into the table `translation__item` and related i18n entries.
     *
     * @throws \Exception
     */
    public function add(string $arrayKey = '', array $valueArr = []): int
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
        INSERT INTO `translation__item`(
            `translation_item_id`, `translation_group_id`, `translation_id`, `array_key`, `sid_name`, `creation_user_id`
        )
        VALUES (
            NULL, ?, ?, ?, ?, ?
        )
        ";

        $parameters = [
            $this->translationGroupId,
            $this->translationId,
            $arrayKey,
            $sidName ?? 0,
            $this->_userId
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit
     *
     * Updates a row in the table `translation__item` and related i18n entries.
     *
     * @throws \Exception
     */
    public function edit(int $itemId = 0, array $valueArr = []): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($itemId);

        foreach ($valueArr as $languageId => $value) {
            $sidName = $i18nModel->editSmall($raw->sid_name, $value, $languageId);
        }

        /*
         * Update entry
         */
        $query = "
        UPDATE `translation__item` 
        SET 
            `sid_name` = ?, 
            `update_user_id` = ?
        WHERE `translation_item_id` = ?
        AND `translation_group_id` = ?
        AND `translation_id` = ?
        ";

        $parameters = [
            $sidName ?? 0,
            $this->_userId,
            $itemId,
            $this->translationGroupId,
            $this->translationId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Delete
     *
     * Delete rows from the table `translation__item` and related i18n entries.
     *
     * @throws \Exception
     */
    public function del(int $itemId = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($itemId);
        $i18nModel->flushSmall($raw->sid_name);

        $query = "DELETE FROM `translation__item` WHERE `translation_item_id` = ? AND `translation_group_id` = ? AND `translation_id` = ?";

        $parameters = [$itemId, $this->translationGroupId, $this->translationId];

        return $this->delete($query, $parameters);
    }

    /**
     * Get raw
     *
     * Retrieve raw data of an existing record.
     */
    public function getRaw(int $id = 0): object|bool
    {
        $query = "SELECT * FROM `translation__item` WHERE `translation_item_id` = ? AND `translation_group_id` = ? AND `translation_id` = ? LIMIT 1";

        $parameters = [$id, $this->translationGroupId, $this->translationId];

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
        $fields = $this->describeTableFields('translation__item');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `call_sign` FROM `translation__item` WHERE `" . $field . "` = ?";

        $parameters = [$value];

        if ($index) {
            $query .= " AND `translation_item_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
