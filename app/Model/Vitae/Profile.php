<?php

namespace Model\Vitae;

use Lib\MySql;

/**
 * Profile
 *
 * Data handler for the MySql table with the same name as this class.
 */
class Profile extends MySql
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
        $this->_dbConn = 'vitae';

        // Initialize database connector
        parent::__construct($this->_dbConn);
    }

    /**
     * Get data object
     *
     * Retrieve the data object of a profile record.
     */
    public function getDataObject(int $profileId = 0): object|bool
    {
        /*
         * Load classes
         */
        $languageModel = new \Model\Core\Language();

        /*
         * Profile row
         */
        $rowObj = $this->getById($profileId);

        /*
         * Translation languages
         */
        $languagesArr = $languageModel->getActive();

        $nameData = [];
        $tooltipData = [];

        foreach ($languagesArr as $language) {

            if ($language->language_id == $this->_languageId) continue;

            $nameData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_name', $profileId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $tooltipData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_tooltip', $profileId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];
        }

        /*
         * Return data
         */
        return (object) [
            'profile_id' => $rowObj->profile_id ?? 0,
            'type' => $rowObj->type ?? '',
            'url' => $rowObj->url ?? '',
            'icon' => $rowObj->icon ?? '',
            'position' => $rowObj->position ?? '',
            'data_i18n_id' => $this->_languageId ?? 0,
            'data_i18n_iso2' => $languageModel->getIso2ById($this->_languageId) ?? '',
            'name' => $rowObj->name ?? '',
            'name_i18n' => $nameData,
            'tooltip' => $rowObj->tooltip ?? '',
            'tooltip_i18n' => $tooltipData,
        ];
    }

    /**
     * Get translation
     *
     * Retrieve a translation value in a specific language.
     */
    private function _getTranslation(string $field = '', int $profileId = 0, string $table = '', string $column = '', int $languageId = 0): ?string
    {
        $query = "
        SELECT `i18nTable`.`value`

        FROM `profile`
        
        LEFT JOIN `$table` AS `i18nTable` ON (
            `i18nTable`.`$column` = `profile`.`$field`
            AND
            `i18nTable`.`language_id` = ?
        )

        WHERE `profile`.`profile_id` = ?
        ";

        $parameters = [$languageId, $profileId];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Count
     *
     * Count all available records.
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM `profile`";

        return (int) $this->fetchValue($query);
    }

    /**
     * Get all
     *
     * Select all available records.
     */
    public function getAll(int $offset = 0, int $rowCount = 0, string $search = '', string $multiSort = 'position ASC', array $multiFilter = []): array
    {
        $query = "
        SELECT
            `profile`.`profile_id`,
            `profile`.`type`,
            `profile`.`url`,
            `profile`.`icon`,
            `profile`.`position`,
            `name_i18n`.`value` AS 'name',
            `tooltip_i18n`.`value` AS 'tooltip'

        FROM `profile`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `profile`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `tooltip_i18n` ON (
            `tooltip_i18n`.`sid` = `profile`.`sid_tooltip`
            AND
            `tooltip_i18n`.`language_id` = ?
        )

        WHERE 1
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId
        ];

        /*
         * Search
         */
        if (!empty($search)) {

            $query .= "
            AND (
                `profile`.`profile_id` LIKE ?
                OR 
                `profile`.`type` LIKE ?
                OR 
                `name_i18n`.`value` LIKE ?
            )
            ";

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
    public function getById(int $profileId = 0): object|bool
    {
        $query = "
        SELECT
            `profile`.`profile_id`,
            `profile`.`type`,
            `profile`.`url`,
            `profile`.`icon`,
            `profile`.`position`,
            `name_i18n`.`value` AS 'name',
            `tooltip_i18n`.`value` AS 'tooltip'

        FROM `profile`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `profile`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `tooltip_i18n` ON (
            `tooltip_i18n`.`sid` = `profile`.`sid_tooltip`
            AND
            `tooltip_i18n`.`language_id` = ?
        )

        WHERE `profile`.`profile_id` = ?

        LIMIT 1
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $profileId
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get by type
     *
     * Retrieve a row using the index column value.
     */
    public function getByType(string $type = ''): array
    {
        $query = "
        SELECT
            `name_i18n`.`value` AS 'name',
            `tooltip_i18n`.`value` AS 'tooltip',
            `profile`.`url`,
            `profile`.`icon`

        FROM `profile`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `profile`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `tooltip_i18n` ON (
            `tooltip_i18n`.`sid` = `profile`.`sid_tooltip`
            AND
            `tooltip_i18n`.`language_id` = ?
        )

        WHERE `profile`.`type` = ?

        ORDER BY `profile`.`position`
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $type
        ];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Get all for select
     *
     * Select all available records for bootstrap select.
     */
    public function getAllForSelect(string $order = 'profile_id ASC'): array
    {
        $returnArr = [];

        $query = "
        SELECT
            `profile`.`profile_id`,
            `name_i18n`.`value` AS 'name'

        FROM `profile`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `profile`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        ORDER BY " . $order . "
        ";

        $parameters = [$this->_languageId];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[$obj->profile_id] = $obj->name;

        return $returnArr;
    }

    /**
     * Get position
     *
     * Retrieve the next record position.
     */
    public function getPosition(): int
    {
        $query = "SELECT IFNULL(max(`position`) + 1, 1) AS 'position' FROM `profile` LIMIT 1";

        $parameters = [];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Update position
     *
     * Updates the column position in the table `profile`.
     *
     * @throws \Exception
     */
    public function updatePosition(int $profileId = 0, int $position = 0): int
    {
        $query = "UPDATE `profile` SET `position` = ? WHERE `profile_id` = ?";

        $parameters = [$position, $profileId];

        return $this->update($query, $parameters);
    }

    /**
     * Add
     *
     * Adds a new row to the table `profile` and related i18n entries.
     *
     * @throws \Exception
     */
    public function add(array $nameArr = [], string $type = '', string $url = '', array $tooltipArr = [], string $icon = '', int $position = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);

        // Name
        $sid = $i18nModel->newSid();
        foreach ($nameArr as $languageId => $name) {
            $sidName = $i18nModel->addSmall($name, $languageId, $sid);
        }

        // Tooltip
        $sid = $i18nModel->newSid();
        foreach ($tooltipArr as $languageId => $tooltip) {
            $sidTooltip = $i18nModel->addSmall($tooltip, $languageId, $sid);
        }

        $query = "
        INSERT INTO `profile` 
            (`profile_id`, `sid_name`, `type`, `url`, `sid_tooltip`, `icon`, `position`, `creation_user_id`)
        VALUES 
            (NULL, ?, ?, ?, ?, ?, ?, ?)
        ";

        $parameters = [
            $sidName ?? 0,
            $type,
            $url,
            $sidTooltip ?? 0,
            $icon,
            $position,
            $this->_userId
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit
     *
     * Updates a row in the table `profile` and related i18n entries.
     *
     * @throws \Exception
     */
    public function edit(int $profileId = 0, array $nameArr = [], string $type = '', string $url = '', array $tooltipArr = [], string $icon = ''): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($profileId);

        // Name
        foreach ($nameArr as $languageId => $name) {
            $sidName = $i18nModel->editSmall($raw->sid_name, $name, $languageId);
        }

        // Tooltip
        foreach ($tooltipArr as $languageId => $tooltip) {
            $sidTooltip = $i18nModel->editSmall($raw->sid_tooltip, $tooltip, $languageId);
        }

        $query = "
        UPDATE `profile` 
        SET 
            `sid_name` = ?,
            `sid_tooltip` = ?,
            `type` = ?,
            `url` = ?,
            `icon` = ?,
            `update_user_id` = ?
        WHERE `profile_id` = ?
        ";

        $parameters = [
            $sidName ?? 0,
            $sidTooltip ?? 0,
            $type,
            $url,
            $icon,
            $this->_userId,
            $profileId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Del
     *
     * Delete a row from the table `profile` and related i18n entries.
     *
     * @throws \Exception
     */
    public function del(int $profileId = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($profileId);
        $i18nModel->flushSmall($raw->sid_name);
        $i18nModel->flushSmall($raw->sid_tooltip);

        $query = "DELETE FROM `profile` WHERE `profile_id` = ?";

        $parameters = [$profileId];

        return $this->delete($query, $parameters);
    }

    /**
     * Del icon
     *
     * Clear the column `icon` of a row in the table `profile`.
     *
     * @throws \Exception
     */
    public function delIcon(int $profileId = 0): int
    {
        $query = "UPDATE `profile` SET `icon` = '' WHERE `profile_id` = ?";

        $parameters = [$profileId];

        return $this->update($query, $parameters);
    }

    /**
     * Get raw
     *
     * Retrieve raw data of an existing record.
     */
    public function getRaw(int $id = 0): object|bool
    {
        $query = "SELECT * FROM `profile` WHERE `profile_id` = ? LIMIT 1";

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
    public function isDuplicated(string $value = '', string $field = '', int $index = 0): string
    {
        $fields = $this->describeTableFields('profile');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `profile_id` FROM `profile` WHERE `" . $field . "` = ?";

        $parameters = [$value];

        if ($index) {
            $query .= " AND `profile_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
