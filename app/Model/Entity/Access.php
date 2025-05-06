<?php

namespace Model\Entity;

use Lib\MySql;

/**
 * Access
 *
 * Helper class to query user access permissions.
 */
class Access extends MySql
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
     * Module
     *
     * Retrieves user permission to the received module id.
     */
    public function module(int $userId = 0, int $moduleId = 0): bool
    {
        $query = "SELECT COUNT(*) FROM `user__permission` WHERE `user_id` = ? AND `module_id` = ?";

        $parameters = [$userId, $moduleId];

        return ($this->fetchValue($query, $parameters) > 0);
    }

}
