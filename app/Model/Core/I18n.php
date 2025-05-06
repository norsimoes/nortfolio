<?php

namespace Model\Core;

use Lib\MySql;

/**
 * I18n
 *
 * Handles the internationalization of contents.
 */
class I18n extends MySql
{
    /**
     * Class constructor
     *
     * @throws \Exception
     */
    public function __construct(string $dbConn = '')
    {
        // Initialize database connector
        parent::__construct($dbConn);
    }

    /**
     * New SID
     *
     * Generates a new SID.
     *
     * @throws \Exception
     */
    public function newSid(): int
    {
        $query = "INSERT INTO `index__sid` (`sid`, `dummy`) VALUES (NULL, 'a')";

        return $this->insert($query);
    }

    /**
     * New MID
     *
     * Generates a new MID.
     *
     * @throws \Exception
     */
    public function newMid(): int
    {
        $query = "INSERT INTO `index__mid` (`mid`, `dummy`) VALUES (NULL, 'a')";

        return $this->insert($query);
    }

    /**
     * New LID
     *
     * Generates a new LID.
     *
     * @throws \Exception
     */
    public function newLid(): int
    {
        $query = "INSERT INTO `index__lid` (`lid`, `dummy`) VALUES (NULL, 'a')";

        return $this->insert($query);
    }

    /**
     * Get small
     *
     * Retrieve an internationalized small text data.
     */
    public function getSmall(int $sid = 0): object|bool
    {
        $query = "SELECT * FROM `i18n__text__small` WHERE `sid` = ? AND `language_id` = ?";

        $parameters = [$sid, $this->_languageId];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get medium
     *
     * Retrieve an internationalized medium text data.
     */
    public function getMedium(int $mid = 0): object|bool
    {
        $query = "SELECT * FROM `i18n__text__medium` WHERE `mid` = ? AND `language_id` = ?";

        $parameters = [$mid, $this->_languageId];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get large
     *
     * Retrieve an internationalized large text data.
     */
    public function getLarge(int $lid = 0): object|bool
    {
        $query = "SELECT * FROM `i18n__text__large` WHERE `lid` = ? AND `language_id` = ?";

        $parameters = [$lid, $this->_languageId];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Add small
     *
     * Inserts a new internationalized small text.
     *
     * @throws \Exception
     */
    public function addSmall(string $value = '', int $languageId = 0, int $sid = 0): int
    {
        // SID
        if (!$sid) $sid = $this->newSid();

        // Language id
        if (!$languageId) $languageId = $this->_languageId;

        // Slug
        $slug = !empty($value) ? (new \Lib\Helper\Text())->slug($value) : '';

        $query = "
        INSERT INTO `i18n__text__small`
            (`id`, `sid`, `language_id`, `value`, `slug`) 
        VALUES 
            (NULL, ?, ?, ?, ?)
        ";

        $parameters = [
            $sid,
            $languageId,
            $value,
            $slug
        ];

        return $this->insert($query, $parameters) ? $sid : 0;
    }

    /**
     * Add medium
     *
     * Inserts a new internationalized medium text.
     *
     * @throws \Exception
     */
    public function addMedium(string $value = '', int $languageId = 0, int $mid = 0): int
    {
        // MID
        if (!$mid) $mid = $this->newMid();

        // Language id
        if (!$languageId) $languageId = $this->_languageId;

        $query = "
        INSERT INTO `i18n__text__medium`
            (`id`, `mid`, `language_id`, `value`)
        VALUES
            (NULL, ?, ?, ?)
        ";

        $parameters = [
            $mid,
            $languageId,
            $value
        ];

        return $this->insert($query, $parameters) ? $mid : 0;
    }

    /**
     * Add large
     *
     * Inserts a new internationalized large text.
     *
     * @throws \Exception
     */
    public function addLarge(string $value = '', int $languageId = 0, int $lid = 0): int
    {
        // LID
        if (!$lid) $lid = $this->newLid();

        // Language id
        if (!$languageId) $languageId = $this->_languageId;

        $query = "
        INSERT INTO `i18n__text__large`
            (`id`, `lid`, `language_id`, `value`)
        VALUES
            (NULL, ?, ?, ?)
        ";

        $parameters = [
            $lid,
            $languageId,
            $value
        ];

        return $this->insert($query, $parameters) ? $lid : 0;
    }

    /**
     * Edit small
     *
     * Update an internationalized small text value.
     *
     * @throws \Exception
     */
    public function editSmall(int $sid = 0, string $value = '', int $languageId = 0): int
    {
        // Language id
        if (!$languageId) $languageId = $this->_languageId;

        // Slug
        $slug = !empty($value) ? (new \Lib\Helper\Text())->slug($value) : '';

        $query = "
        UPDATE `i18n__text__small`
        SET
            `value` = ?,
            `slug` =?
        WHERE `sid` = ?
        AND `language_id` = ?
        ";

        $parameters = [
            $value,
            $slug,
            $sid,
            $languageId
        ];

        return $this->update($query, $parameters) ? $sid : 0;
    }

    /**
     * Edit medium
     *
     * Update an internationalized medium text value.
     *
     * @throws \Exception
     */
    public function editMedium(int $mid = 0, string $value = '', int $languageId = 0): int
    {
        // Language id
        if (!$languageId) $languageId = $this->_languageId;

        $query = "
        UPDATE `i18n__text__medium`
        SET
            `value` = ?
        WHERE `mid` = ?
        AND `language_id` = ?
        ";

        $parameters = [
            $value,
            $mid,
            $languageId
        ];

        return $this->update($query, $parameters) ? $mid : 0;
    }

    /**
     * Edit large
     *
     * Update an internationalized large text value.
     *
     * @throws \Exception
     */
    public function editLarge(int $lid = 0, string $value = '', int $languageId = 0): int
    {
        // Language id
        if (!$languageId) $languageId = $this->_languageId;

        $query = "
        UPDATE `i18n__text__large`
        SET
            `value` =?
        WHERE `lid` =?
        AND `language_id` =?
        ";

        $parameters = [
            $value,
            $lid,
            $languageId
        ];

        return $this->update($query, $parameters) ? $lid : 0;
    }

    /**
     * Del small
     *
     * Deletes an internationalized small text entry.
     *
     * @throws \Exception
     */
    public function delSmall(int $sid = 0, int $languageId = 0): int
    {
        if (!$languageId) $languageId = $this->_languageId;

        $query = "DELETE FROM `i18n__text__small` WHERE `sid` = ? AND `language_id` = ?";

        $parameters = [$sid, $languageId];

        return $this->delete($query, $parameters);
    }

    /**
     * Del medium
     *
     * Deletes an internationalized medium text entry.
     *
     * @throws \Exception
     */
    public function delMedium(int $mid = 0, int $languageId = 0): int
    {
        if (!$languageId) $languageId = $this->_languageId;

        $query = "DELETE FROM `i18n__text__medium` WHERE `mid` = ? AND `language_id` = ?";

        $parameters = [$mid, $languageId];

        return $this->delete($query, $parameters);
    }

    /**
     * Del large
     *
     * Deletes an internationalized large text entry.
     *
     * @throws \Exception
     */
    public function delLarge(int $lid = 0, int $languageId = 0): int
    {
        if (!$languageId) $languageId = $this->_languageId;

        $query = "DELETE FROM `i18n__text__large` WHERE `lid` = ? AND `language_id` = ?";

        $parameters = [$lid, $languageId];

        return $this->delete($query, $parameters);
    }

    /**
     * Flush small
     *
     * Deletes all the internationalized small text entries matching the received index.
     *
     * @throws \Exception
     */
    public function flushSmall(int $sid = 0): int
    {
        $query = "DELETE FROM `i18n__text__small` WHERE `sid` = ?";

        $parameters = [$sid];

        return $this->delete($query, $parameters);
    }

    /**
     * Flush medium
     *
     * Deletes all the internationalized medium text entries matching the received index.
     *
     * @throws \Exception
     */
    public function flushMedium(int $mid = 0): int
    {
        $query = "DELETE FROM `i18n__text__medium` WHERE `mid` = ?";

        $parameters = [$mid];

        return $this->delete($query, $parameters);
    }

    /**
     * Flush large
     *
     * Deletes all the internationalized large text entries matching the received index.
     *
     * @throws \Exception
     */
    public function flushLarge(int $lid = 0): int
    {
        $query = "DELETE FROM `i18n__text__large` WHERE `lid` = ?";

        $parameters = [$lid];

        return $this->delete($query, $parameters);
    }

}
