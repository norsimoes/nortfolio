<?php

namespace Model\Entity;

use Lib\MySql;

/**
 * Authentication
 *
 * Data handler for the MySql table with the same name as this class.
 */
class Authentication extends MySql
{
    /**
     * Class constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        // Initialize database
        parent::__construct('core');
    }

    /**
     * Get by username
     *
     * Collect user authentication data using the username.
     */
    public function getByUsername(string $username = ''): object|bool
    {
        $query = "SELECT * FROM `user__authentication` WHERE `username` = ?";

        $parameters = [$username];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Add
     *
     * Inserts a new row into the table `user__authentication.
     *
     * @throws \Exception
     */
    public function add(int $userId = 0, string $username = '', string $password = '', string $salt = ''): int
    {
        $query = "
        INSERT INTO `user__authentication` (
            `user_authentication_id`, `user_id`, `username`, `password`, `salt`
        )
        VALUES (
            NULL, ?, ?, ?, ?
        )
        ";

        $parameters = [
            $userId,
            $username,
            $password,
            $salt,
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit auth
     *
     * Updates the authentication data in the table `user__authentication`.
     *
     * @throws \Exception
     */
    public function editAuth(int $userId = 0, string $email = ''): int
    {
        $query = "UPDATE `user__authentication` SET `username` = ? WHERE  `user_id` = ?";

        $parameters = [$email, $userId];

        return $this->update($query, $parameters);
    }

    /**
     * Edit pass
     *
     * Updates the password data in the table `user__authentication`.
     *
     * @throws \Exception
     */
    public function editPass(int $userId = 0, string $pass = '', string $salt = ''): int
    {
        $query = "UPDATE `user__authentication` SET `password` = ?, `salt` = ? WHERE  `user_id` = ?";

        $parameters = [$pass, $salt, $userId];

        return $this->update($query, $parameters);
    }

    /**
     * Del
     *
     * Delete a row from the table `user__authentication`.
     *
     * @throws \Exception
     */
    public function del(int $userId = 0): int
    {
        $query = "DELETE FROM `user__authentication` WHERE `user_id` = ?";

        $parameters = [$userId];

        return $this->delete($query, $parameters);
    }

    /**
     * Log
     *
     * Add a new log entry to the database.
     *
     * @throws \Exception
     */
    public function log(int $authenticationId = 0, string $type = '', int $wasSuccessful = 0, string $description = ''): int
    {
        $query = "
        INSERT INTO `user__authentication_log` (
            `id`, `user_authentication_id`, `type`, `was_successful`, `description`, `date_created`
        )
        VALUES (
            NULL, ?, ?, ?, ?, CURRENT_TIMESTAMP
        )
        ";

        $parameters = [$authenticationId, $type, $wasSuccessful, $description];

        return $this->insert($query, $parameters);
    }

}
