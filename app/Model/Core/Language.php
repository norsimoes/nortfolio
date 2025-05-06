<?php

namespace Model\Core;

use Lib\MySql;

/**
 * Language
 *
 * Data handler for the MySql table with the same name as this class.
 */
class Language extends MySql
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
     * Retrieve the data object of a language record.
     */
    public function getDataObject(int $languageId = 0): ?object
    {
        // Role object
        $rowObj = $this->getById($languageId);

        /*
         * Return data
         */
        return (object) [
            'language_id' => $rowObj->language_id ?? 0,
            'reference_name' => $rowObj->reference_name ?? '',
            'local_name' => $rowObj->local_name ?? '',
            'iso2' => $rowObj->iso2 ?? '',
            'iso3' => $rowObj->iso3 ?? '',
            'is_active' => $rowObj->is_active ?? 1,
            'data_i18n_id' => $this->_languageId ?? 0,
            'data_i18n_iso2' => $this->getIso2ById($this->_languageId) ?? '',
        ];
    }

    /**
     * Count
     *
     * Count all available records.
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM `language`";

        return (int) $this->fetchValue($query);
    }

    /**
     * Count active
     *
     * Count all available records.
     */
    public function countActive(): int
    {
        $query = "SELECT COUNT(*) as total FROM `language` WHERE `is_active` = 1";

        return (int) $this->fetchValue($query);
    }

    /**
     * Get all
     *
     * Select all available records.
     */
    public function getAll(int $offset = 0, int $rowCount = 0, string $search = '', string $multiSort = 'language_id ASC', ?array $multiFilter = null): array
    {
        $query = "
        SELECT
            `language`.`language_id`,
            `language`.`reference_name`,
            `language`.`local_name`,
            `language`.`iso2`,
            `language`.`iso3`,
            `language`.`is_active`

        FROM `language`

        WHERE 1
        ";

        $parameters = [];

        /*
         * Search
         */
        if (!empty($search)) {

            $query .= "
            AND (
                `language`.`language_id` LIKE ?
                OR 
                `language`.`reference_name` LIKE ?
                OR 
                `language`.`local_name` LIKE ?
                OR 
                `language`.`iso2` LIKE ?
                OR 
                `language`.`iso3` LIKE ?
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
    public function getById(int $languageId = 0): object|bool
    {
        $query = "
        SELECT
            `language`.`language_id`,
            `language`.`reference_name`,
            `language`.`local_name`,
            `language`.`iso2`,
            `language`.`iso3`,
            `language`.`is_active`

        FROM `language`

        WHERE `language`.`language_id` = ?

        LIMIT 1
        ";

        $parameters = [
            $languageId
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get active
     *
     * Retrieve an array of all active languages from table `language`.
     */
    public function getActive(): array
    {
        $query = "SELECT * FROM `language` WHERE `is_active` = 1";

        $parameters = [];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Get id by iso3
     *
     * Retrieve a language id value by supplying its iso3.
     */
    public function getIdByIso3(string $iso3 = ''): string
    {
        $query = "SELECT `language_id` FROM `language` WHERE `iso3` = ? LIMIT 1";

        $parameters = [$iso3];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Get iso2 by iso3
     *
     * Retrieve a language iso2 value by supplying its iso3.
     */
    public function getIso2ByIso3(string $iso3 = ''): string
    {
        $query = "SELECT `iso2` FROM `language` WHERE `iso3` = ? LIMIT 1";

        $parameters = [$iso3];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Get iso2 by language id
     *
     * Retrieve a language iso2 value by supplying its language id.
     */
    public function getIso2ById(int $languageId = 0): string
    {
        $query = "SELECT `iso2` FROM `language` WHERE `language_id` = ? LIMIT 1";

        $parameters = [$languageId];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Get iso3 by language id
     *
     * Retrieve a language iso3 value by supplying its language id.
     */
    public function getIso3ById(int $languageId = 0): string
    {
        $query = "SELECT `iso3` FROM `language` WHERE `language_id` = ? LIMIT 1";

        $parameters = [$languageId];

        return $this->fetchValue($query, $parameters);
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
            `language`.`language_id`,
            `language`.`local_name`

        FROM `language`

        WHERE `language`.`is_active` = 1

        ORDER BY `language`.`language_id`
        ";

        $parameters = [];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[$obj->language_id] = $obj->local_name;

        return $returnArr;
    }

    /**
     * Get active status
     *
     * Retrieve the active status of a database row.
     */
    public function getActiveStatus(int $id = 0): int
    {
        $query = "SELECT `is_active` FROM `language` WHERE `language_id` = ?";

        $parameters = [$id];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Set active status
     *
     * Change the active status of a database row.
     *
     * @throws \Exception
     */
    public function setActiveStatus(int $id = 0, string $status = ''): int
    {
        $query = "UPDATE `language` SET `is_active` = ? WHERE `language_id` = ?";

        $parameters = [$status, $id];

        return $this->update($query, $parameters);
    }

    /**
     * Get status
     *
     * Retrieve the status of a database row.
     */
    public function getStatus(int $id = 0): int
    {
        $query = "SELECT `is_active` FROM `language` WHERE `language_id` = ?";

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
    public function setStatus(int $id = 0, string $newStatus = ''): int
    {
        $query = "UPDATE `language` SET `is_active` = ? WHERE `language_id` = ?";

        $parameters = [$newStatus, $id];

        return $this->update($query, $parameters);
    }

    /**
     * Add
     *
     * Inserts a new row into the table `language`.
     *
     * @throws \Exception
     */
    public function add(string $name = '', string $localName = '', string $iso2 = '', string $iso3 = '', int $isActive = 0): int
    {
        $query = "
        INSERT INTO `language`(
            `language_id`, 
            `reference_name`, 
            `local_name`, 
            `iso2`,
            `iso3`,
            `is_active`,
            `creation_user_id`
        )
        VALUES (
            NULL, ?, ?, ?, ?, ?, ?
        )
        ";

        $parameters = [
            $name,
            $localName,
            $iso2,
            $iso3,
            $isActive,
            $this->_userId
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit
     *
     * Updates a row in the table `language`.
     *
     * @throws \Exception
     */
    public function edit(int $languageId = 0, string $name = '', string $localName = '', string $iso2 = '', string $iso3 = '', int $isActive = 0): int
    {
        $query = "
        UPDATE `language`
        SET 
            `reference_name` = ?,
            `local_name` = ?,
            `iso2` = ?,
            `iso3` = ?,
            `is_active` = ?
        WHERE `language_id` = ?
        ";

        $parameters = [
            $name,
            $localName,
            $iso2,
            $iso3,
            $isActive,
            $languageId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Delete
     *
     * Deletes a row from the table `language`.
     *
     * @throws \Exception
     */
    public function del(int $languageId = 0): int
    {
        $query = "DELETE FROM `language` WHERE `language_id` = ?";

        $parameters = [$languageId];

        return $this->delete($query, $parameters);
    }

    /**
     * Get position
     *
     * Retrieve the next language position in the table `domain__language`.
     */
    public function getPosition(): int
    {
        $query = "SELECT IFNULL(max(`position`) + 1, 1) AS 'position' FROM `domain__language` LIMIT 1";

        $parameters = [];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Update position
     *
     * Updates the column position in the table `domain__language`.
     *
     * @throws \Exception
     */
    public function updatePosition(int $languageId = 0, int $position = 0): int
    {
        $query = "UPDATE `domain__language` SET `position` = ? WHERE `language_id` = ?";

        $parameters = [$position, $languageId];

        return $this->update($query, $parameters);
    }

    /**
     * Get raw
     *
     * Retrieve raw data of an existing record.
     */
    public function getRaw(int $id = 0): object|bool
    {
        $query = "SELECT * FROM `language` WHERE `language_id` = ? LIMIT 1";

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
    public function isDuplicated(string $value = '', string $field = '', int $index = 0): string|bool
    {
        $fields = $this->describeTableFields('language');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `language_id` FROM `language` WHERE `" . $field . "` = ?";

        $parameters = [$value];

        if ($index) {
            $query .= " AND `language_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
