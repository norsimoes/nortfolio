<?php

/* ----------------------------------------------------------------------------
 * PDO Connection test
 *
 * If the application is set to use a database connection, we test the connection
 * to ensure that there will be no issues when using it to retrieve contents.
 * ----------------------------------------------------------------------------
 */
if (isset($GLOBALS['db']) && is_array($GLOBALS['db'])) {

    reset($GLOBALS['db']);

    $conn = $GLOBALS['db'][key($GLOBALS['db'])];

    try {

        $dbh = new \pdo(
            'mysql:host=' . $conn['host'] . ';port=' . $conn['port'] . ';dbname=' . $conn['name'],
            $conn['user'],
            $conn['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        unset($dbh);

    } catch (\PDOException $e) {

        die('Unable to test database connection: ' . $e->getMessage());
    }
}
