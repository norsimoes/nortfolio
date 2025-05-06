<?php

namespace Lib;

use PDO;
use PDOStatement;

/**
 * MySql
 *
 * MySQL PDO database handler.
 */
class MySql
{
    /*
     * Database connection errors
     */
    public const DB_CONFIG_FAIL = 'Unknown database configuration: ';
    public const DB_CONN_FAIL = 'Database connection error: ';
    public const DB_PARAM_FAIL = 'Database parameter missing: ';
    public const DB_QUERY_FAIL = 'Database query error: ';

    /*
     * Class properties
     */
    protected int $_userId = 0;
    protected int $_languageId = 0;
    private string $_host = '';
    private string $_port = '';
    private string $_name = '';
    private string $_user = '';
    private string $_pass = '';
    protected ?object $_dbh = null;

    /**
     * Initializes a database connection using received access data.
     *
     * @throws \Exception
     */
    public function __construct(string $dbConn = '')
    {
        if (!isset($GLOBALS['db'][$dbConn])) throw new \Exception(self::DB_CONFIG_FAIL . $dbConn);

        $this->_setUserId();
        $this->_setLanguageId();

        $dbConn = $GLOBALS['db'][$dbConn];

        $this->_host = $dbConn['host'];
        $this->_port = $dbConn['port'];
        $this->_name = $dbConn['name'];
        $this->_user = $dbConn['user'];
        $this->_pass = $dbConn['pass'];

        $this->_dbh = $this->initConnection();
    }

    /**
     * Sets the logged user id.
     *
     * @throws \Exception
     */
    protected function _setUserId(): void
    {
        $userObj = \Lib\Session::getInstance()->get('user');

        $this->_userId = $userObj->user_id ?? 0;
    }

    /**
     * Sets the logged user language id or defaults to the app language id.
     *
     * @throws \Exception
     */
    protected function _setLanguageId(): void
    {
        $session = \Lib\Session::getInstance();

        $userObj = $session->get('user');

        $sessionLanguageId = $session->getI18n('language_id');

        $this->_languageId = $userObj->language_id ?? $sessionLanguageId;
    }

    /**
     * Instantiates a new database connection.
     *
     * @throws \Exception
     */
    private function initConnection(): PDO
    {
        if (empty($this->_host)) throw new \Exception(self::DB_PARAM_FAIL . 'host');
        if (empty($this->_port)) throw new \Exception(self::DB_PARAM_FAIL . 'port');
        if (empty($this->_name)) throw new \Exception(self::DB_PARAM_FAIL . 'name');
        if (empty($this->_user)) throw new \Exception(self::DB_PARAM_FAIL . 'user');
        if (empty($this->_pass)) throw new \Exception(self::DB_PARAM_FAIL . 'pass');

        try {

            return new PDO(
                "mysql: host=" . $this->_host . "; port=" . $this->_port . "; dbname=" . $this->_name . '; charset=UTF8',
                $this->_user,
                $this->_pass,
                [
                    PDO::ATTR_PERSISTENT => false,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ]
            );

        } catch (\PDOException $e) {

            throw new \Exception(self::DB_CONN_FAIL . ' ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a value from a single field.
     */
    public function fetchValue(string $query = '', array $parameters = []): ?string
    {
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute($parameters);

        return $stmt->fetchColumn();
    }

    /**
     * Retrieve a single record from the database.
     */
    public function fetchObject(string $query = '', array $parameters = []): object|bool
    {
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute($parameters);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Returns an array with an object for each row.
     */
    public function fetchAllObject(string $query = '', array $parameters = []): array
    {
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute($parameters);

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    /**
     * Execute an insert query with optional bound parameters returning the last inserted id.
     *
     * @throws \Exception
     */
    public function insert(string $query = '', array $parameters = []): int
    {
        try {

            $stmt = $this->_dbh->prepare($query);
            $stmt->execute($parameters);

            $lastInsertId = $this->_dbh->lastInsertId();

            return (int) $lastInsertId;

        } catch (\PDOException $e) {

            throw new \Exception(self::DB_QUERY_FAIL . ' ' . $e->getMessage());
        }
    }

    /**
     * Execute an update query with optional bound parameters returning the number of affected rows.
     *
     * @throws \Exception
     */
    public function update(string $query = '', array $parameters = [], bool $affectedRows = false, bool $lastInsertId = false): int
    {
        try {

            $stmt = $this->_dbh->prepare($query);
            $status = $stmt->execute($parameters);

            if ($lastInsertId) return (int) $this->_dbh->lastInsertId();

            if ($affectedRows) return (int) $stmt->rowCount();

            return (int) $status;

        } catch (\PDOException $e) {

            throw new \Exception(self::DB_QUERY_FAIL . ' ' . $e->getMessage());
        }
    }

    /**
     * Execute a delete query with optional bound parameters returning the number of affected rows.
     *
     * @throws \Exception
     */
    public function delete(string $query = '', array $parameters = []): int
    {
        try {

            $stmt = $this->_dbh->prepare($query);
            $stmt->execute($parameters);

            $count = $stmt->rowCount();

            return (int) $count;

        } catch (\PDOException $e) {

            throw new \Exception(self::DB_QUERY_FAIL . ' ' . $e->getMessage());
        }
    }

    /**
     * Change the active database name.
     */
    public function changeDb(string $dbName = ''): PDOStatement
    {
        $query = "USE " . $dbName;

        return $this->_dbh->query($query);
    }

    /**
     * Returns the database columns names.
     */
    public function describeTableFields(string $tableName = ''): array
    {
        $query = "DESCRIBE `" . $tableName . "`";

        $rows = $this->fetchAllObject($query);

        $fields = [];

        if (!empty($rows)) {

            foreach ($rows as $row) {

                $fields[] = $row->Field;
            }
        }

        return $fields;
    }

    /**
     * Returns the database columns data.
     */
    public function describeTable(string $tableName = ''): array
    {
        $query = "DESCRIBE `" . $tableName . "`";

        return $this->fetchAllObject($query);
    }

}
