<?php

namespace Model\Entity;

use Lib\MySql;

/**
 * User
 *
 * Data handler for the MySql table with the same name as this class.
 */
class User extends MySql
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
     * Retrieve the data object of a user record.
     */
    public function getDataObject(int $userId = 0): object|null
    {
        // Load model class
        $languageModel = new \Model\Core\Language();

        // User row
        $rowObj = $this->getById($userId);

        /*
         * Return data
         */
        return (object) [
            'user_id' => $rowObj->user_id ?? 0,
            'role_id' => $rowObj->role_id ?? 0,
            'language_id' => $rowObj->language_id ?? 0,
            'name' => $rowObj->name ?? '',
            'email' => $rowObj->email ?? '',
            'phone' => $rowObj->phone ?? '',
            'avatar' => $rowObj->avatar ?? '',
            'status' => $rowObj->status ?? '',
            'role_name' => $rowObj->role_name ?? '',
            'data_i18n_id' => $this->_languageId ?? 0,
            'data_i18n_iso2' => $languageModel->getIso2ById($this->_languageId) ?? '',
        ];
    }

    /**
     * Count
     *
     * Count all available records.
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM `user`";

        return (int) $this->fetchValue($query);
    }

    /**
     * Get all
     *
     * Select all available records.
     */
    public function getAll(int $offset = 0, int $rowCount = 0, string $search = '', string $multiSort = 'user_id DESC', array $multiFilter = []): array
    {
        $query = "
        SELECT
            `user`.`user_id`,
            `user`.`role_id`,
            `user`.`language_id`,
            `user`.`name`,
            `user`.`email`,
            `user`.`phone`,
            `user`.`avatar`,
            `user`.`status`,
            `role_i18n`.`value` AS 'role_name'

        FROM `user`

        INNER JOIN `role` USING (`role_id`)
        
        LEFT JOIN `i18n__text__small` AS `role_i18n` ON (
            `role_i18n`.`sid` = `role`.`sid_name`
            AND
            `role_i18n`.`language_id` = ?
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
                `user`.`user_id` LIKE ?
                OR
                `user`.`name` LIKE ?
                OR
                `user`.`email` LIKE ?
                OR
                `user`.`phone` LIKE ?
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
    public function getById(int $userId = 0): object|bool
    {
        $query = "
        SELECT
            `user`.`user_id`,
            `user`.`role_id`,
            `user`.`language_id`,
            `user`.`name`,
            `user`.`email`,
            `user`.`phone`,
            `user`.`avatar`,
            `user`.`status`,
            `role_i18n`.`value` AS 'role_name'

        FROM `user`

        INNER JOIN `role` USING (`role_id`)
        
        LEFT JOIN `i18n__text__small` AS `role_i18n` ON (
            `role_i18n`.`sid` = `role`.`sid_name`
            AND
            `role_i18n`.`language_id` = ?
        )

        WHERE `user_id` = ?

        LIMIT 1
        ";

        $parameters = [
            $this->_languageId,
            $userId
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get by role id
     *
     * Get all users associated with the given role id.
     */
    public function getByRoleId(int $roleId = 0): array
    {
        $query = "
        SELECT
            `user`.`user_id`,
            `user`.`name`
        FROM `user`
        WHERE `user`.`role_id` = ?
        AND `user`.`status` = 1
        ";

        $parameters = [
            $roleId
        ];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Get status
     *
     * Retrieve the status of a database row.
     */
    public function getStatus(int $id = 0): int
    {
        $query = "SELECT `status` FROM `user` WHERE `user_id` = ?";

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
        $query = "UPDATE `user` SET `status` = ? WHERE `user_id` = ?";

        $parameters = [$newStatus, $id];

        return $this->update($query, $parameters);
    }

    /**
     * Add
     *
     * Inserts a new row into the table `user`.
     *
     * @throws \Exception
     */
    public function add(int $roleId = 0, int $languageId = 0, string $name = '', string $email = '', string $phone = '', int $status = 0): int
    {
        $query = "
        INSERT INTO `user`(
            `user_id`, `role_id`, `language_id`, `name`, `email`, `phone`, `status`
        )
        VALUES (
            NULL, ?, ?, ?, ?, ?, ?
        )
        ";

        $parameters = [
            $roleId,
            $languageId,
            $name,
            $email,
            $phone,
            $status,
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit
     *
     * Updates a row in the table `user`.
     *
     * @throws \Exception
     */
    public function edit(int $userId = 0, int $roleId = 0, int $languageId = 0, string $name = '', string $email = '', string $phone = '', int $status = 0): int
    {
        $query = "
        UPDATE `user` 
        SET 
            `role_id` = ?, 
            `language_id` = ?,
            `name` = ?,
            `email` = ?,
            `phone` = ?,
            `status` = ?
        WHERE `user_id` = ?
        ";

        $parameters = [
            $roleId,
            $languageId,
            $name,
            $email,
            $phone,
            $status,
            $userId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Edit language id
     *
     * Updates the column `language id` in the table `user`.
     *
     * @throws \Exception
     */
    public function editLanguageId(int $userId = 0, int $languageId = 0): int
    {
        $query = "UPDATE `user` SET `language_id` = ? WHERE `user_id` = ?";

        $parameters = [$languageId, $userId];

        return $this->update($query, $parameters);
    }

    /**
     * Del
     *
     * Delete a row from the table `user`.
     *
     * @throws \Exception
     */
    public function del(int $userId = 0): int
    {
        $query = "DELETE FROM `user` WHERE `user_id` = ?";

        $parameters = [$userId];

        return $this->delete($query, $parameters);
    }

    /**
     * Get permission
     *
     * Retrieves the registered user permissions.
     */
    public function getPermission(int $userId = 0): array
    {
        $query = "SELECT `module_id` FROM `user__permission` WHERE `user_id` = ?";

        $parameters = [$userId];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Get user permission
     *
     * Retrieves the registered user permissions.
     */
    public function getUserPermission(int $userId = 0): array
    {
        $returnArr = [];

        $query = "SELECT * FROM `user__permission` WHERE `user_id` = ?";

        $parameters = [$userId];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[] = $obj->module_id;

        return $returnArr;
    }

    /**
     * Add permission
     *
     * Inserts a new user permission into the table `user__permission`.
     *
     * @throws \Exception
     */
    public function addPermission(int $userId = 0, int $moduleId = 0): int
    {
        $query = "
        INSERT INTO `user__permission`
            (`user_permission_id`, `user_id`, `module_id`)
        VALUES
            (NULL, ?, ?)
        ";

        $parameters = [$userId, $moduleId];

        return $this->insert($query, $parameters);
    }

    /**
     * Del permission
     *
     * Deletes a row from the table `user__permission`.
     *
     * @throws \Exception
     */
    public function delPermission(int $userId = 0, int $moduleId = 0): int
    {
        $query = "DELETE FROM `user__permission` WHERE `user_id` = ? AND `module_id` = ?";

        $parameters = [$userId, $moduleId];

        return $this->delete($query, $parameters);
    }

    /**
     * Flush permission
     *
     * Deletes all user rows from the table `user__permission`.
     *
     * @throws \Exception
     */
    public function flushPermission(int $userId = 0): int
    {
        $query = "DELETE FROM `user__permission` WHERE `user_id` = ?";

        $parameters = [$userId];

        return $this->delete($query, $parameters);
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
        $fields = $this->describeTableFields('user');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `email` FROM `user` WHERE `" . $field . "` = ?";

        $parameters = [$value];

        if ($index) {
            $query .= " AND `user_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
