<?php

namespace Model\Core;

use Lib\MySql;

class Router extends MySql
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
     * Get by path
     *
     * Collect the route from the database using the current URL path.
     */
    public function getByPath(string $path = ''): string
    {
        // Search for an exact match
        $query = "
        SELECT `route`
        FROM `module__route`
        WHERE `slug` = ?
        LIMIT 1
        ";

        $parameters = [$path];

        $route = $this->fetchValue($query, $parameters);

        if (empty($route)) {

            // Search for the best match
            $query = "
            SELECT `route`
            FROM `module__route`
            WHERE ? = `slug`
            OR ? LIKE CONCAT(`slug`, '%')
            ORDER BY LENGTH(`route`) DESC
            LIMIT 1
            ";

            $parameters = [
                $path,
                $path
            ];

            $route = $this->fetchValue($query, $parameters);
        }

        return $route;
    }

}
