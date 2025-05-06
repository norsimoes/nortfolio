<?php

namespace Model\Vitae;

use Lib\MySql;

/**
 * Experience
 *
 * Data handler for the MySql table with the same name as this class.
 */
class Experience extends MySql
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
     * Retrieve the data object of an experience record.
     */
    public function getDataObject(int $experienceId = 0): object|bool
    {
        /*
         * Load classes
         */
        $languageModel = new \Model\Core\Language();

        /*
         * Experience row
         */
        $rowObj = $this->getById($experienceId);

        /*
         * Translation languages
         */
        $languagesArr = $languageModel->getActive();

        $nameData = [];
        $startData = [];
        $endData = [];
        $companyData = [];
        $locationData = [];
        $descData = [];

        foreach ($languagesArr as $language) {

            if ($language->language_id == $this->_languageId) continue;

            $nameData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_name', $experienceId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $startData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_start', $experienceId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $endData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_end', $experienceId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $companyData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_company', $experienceId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $locationData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_location', $experienceId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $descData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('mid_desc', $experienceId, 'i18n__text__medium', 'mid', $language->language_id) ?? '',
            ];
        }

        /*
         * Return data
         */
        return (object) [
            'experience_id' => $rowObj->experience_id ?? 0,
            'position' => $rowObj->position ?? '',
            'tech' => $rowObj->tech ?? ' ',
            'data_i18n_id' => $this->_languageId ?? 0,
            'data_i18n_iso2' => $languageModel->getIso2ById($this->_languageId) ?? '',
            'name' => $rowObj->name ?? '',
            'name_i18n' => $nameData,
            'start' => $rowObj->start ?? '',
            'start_i18n' => $startData,
            'end' => $rowObj->end ?? '',
            'end_i18n' => $endData,
            'company' => $rowObj->company ?? '',
            'company_i18n' => $companyData,
            'location' => $rowObj->location ?? '',
            'location_i18n' => $locationData,
            'description' => $rowObj->description ?? '',
            'description_i18n' => $descData
        ];
    }

    /**
     * Get translation
     *
     * Retrieve a translation value in a specific language.
     */
    private function _getTranslation(string $field = '', int $experienceId = 0, string $table = '', string $column = '', int $languageId = 0): string
    {
        $query = "
        SELECT `i18nTable`.`value`

        FROM `experience`
        
        LEFT JOIN `$table` AS `i18nTable` ON (
            `i18nTable`.`$column` = `experience`.`$field`
            AND
            `i18nTable`.`language_id` = ?
        )

        WHERE `experience`.`experience_id` = ?
        ";

        $parameters = [$languageId, $experienceId];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Count
     *
     * Count all available records.
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM `experience`";

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
            `experience`.`experience_id`,
            `experience`.`position`,
            `experience`.`tech`,
            `name_i18n`.`value` AS 'name',
            `start_i18n`.`value` AS 'start',
            `end_i18n`.`value` AS 'end',
            `company_i18n`.`value` AS 'company',
            `location_i18n`.`value` AS 'location',
            `desc_i18n`.`value` AS 'description'

        FROM `experience`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `experience`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `start_i18n` ON (
            `start_i18n`.`sid` = `experience`.`sid_start`
            AND
            `start_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `end_i18n` ON (
            `end_i18n`.`sid` = `experience`.`sid_end`
            AND
            `end_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `company_i18n` ON (
            `company_i18n`.`sid` = `experience`.`sid_company`
            AND
            `company_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `location_i18n` ON (
            `location_i18n`.`sid` = `experience`.`sid_location`
            AND
            `location_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = `experience`.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
        )

        WHERE 1
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $this->_languageId
        ];

        /*
         * Search
         */
        if (!empty($search)) {

            $query .= "
            AND (
                `experience`.`experience_id` LIKE ?
                OR 
                `name_i18n`.`value` LIKE ?
                OR 
                `desc_i18n`.`value` LIKE ?
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
    public function getById(int $experienceId = 0): object|bool
    {
        $query = "
        SELECT
            `experience`.`experience_id`,
            `experience`.`position`,
            `experience`.`tech`,
            `name_i18n`.`value` AS 'name',
            `start_i18n`.`value` AS 'start',
            `end_i18n`.`value` AS 'end',
            `company_i18n`.`value` AS 'company',
            `location_i18n`.`value` AS 'location',
            `desc_i18n`.`value` AS 'description'

        FROM `experience`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `experience`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `start_i18n` ON (
            `start_i18n`.`sid` = `experience`.`sid_start`
            AND
            `start_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `end_i18n` ON (
            `end_i18n`.`sid` = `experience`.`sid_end`
            AND
            `end_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `company_i18n` ON (
            `company_i18n`.`sid` = `experience`.`sid_company`
            AND
            `company_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `location_i18n` ON (
            `location_i18n`.`sid` = `experience`.`sid_location`
            AND
            `location_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = `experience`.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
        )

        WHERE `experience`.`experience_id` = ?

        LIMIT 1
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $experienceId
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get all for select
     *
     * Select all available records for bootstrap select.
     */
    public function getAllForSelect(string $order = 'experience_id ASC'): array
    {
        $returnArr = [];

        $query = "
        SELECT
            `experience`.`experience_id`,
            `name_i18n`.`value` AS 'name'

        FROM `experience`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `experience`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        ORDER BY " . $order . "
        ";

        $parameters = [$this->_languageId];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[$obj->experience_id] = $obj->name;

        return $returnArr;
    }

    /**
     * Get position
     *
     * Retrieve the next record position.
     */
    public function getPosition(): int
    {
        $query = "SELECT IFNULL(max(`position`) + 1, 1) AS 'position' FROM `experience` LIMIT 1";

        $parameters = [];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Update position
     *
     * Updates the column position in the table `experience`.
     *
     * @throws \Exception
     */
    public function updatePosition(int $experienceId = 0, int $position = 0): int
    {
        $query = "UPDATE `experience` SET `position` = ? WHERE `experience_id` = ?";

        $parameters = [$position, $experienceId];

        return $this->update($query, $parameters);
    }

    /**
     * Add
     *
     * Adds a new row to the table `experience` and related i18n entries.
     *
     * @throws \Exception
     */
    public function add(
        array $nameArr = [],
        array $startArr = [],
        array $endArr = [],
        array $companyArr = [],
        array $locationArr = [],
        array $descArr = [],
        string $tech = '',
        int $position = 0
    ): int {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);

        // Name
        $sid = $i18nModel->newSid();
        foreach ($nameArr as $languageId => $name) {
            $sidName = $i18nModel->addSmall($name, $languageId, $sid);
        }

        // Start
        $sid = $i18nModel->newSid();
        foreach ($startArr as $languageId => $start) {
            $sidStart = $i18nModel->addSmall($start, $languageId, $sid);
        }

        // End
        $sid = $i18nModel->newSid();
        foreach ($endArr as $languageId => $end) {
            $sidEnd = $i18nModel->addSmall($end, $languageId, $sid);
        }

        // Company
        $sid = $i18nModel->newSid();
        foreach ($companyArr as $languageId => $company) {
            $sidCompany = $i18nModel->addSmall($company, $languageId, $sid);
        }

        // Location
        $sid = $i18nModel->newSid();
        foreach ($locationArr as $languageId => $location) {
            $sidLocation = $i18nModel->addSmall($location, $languageId, $sid);
        }

        // Description
        $mid = $i18nModel->newMid();
        foreach ($descArr as $languageId => $desc) {
            $midDesc = $i18nModel->addMedium($desc, $languageId, $mid);
        }

        $query = "
        INSERT INTO `experience` 
            (`experience_id`, `sid_name`, `sid_start`, `sid_end`, `sid_company`, `sid_location`, `mid_desc`, `tech`, `position`, `creation_user_id`)
        VALUES 
            (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $parameters = [
            $sidName ?? 0,
            $sidStart ?? 0,
            $sidEnd ?? 0,
            $sidCompany ?? 0,
            $sidLocation ?? 0,
            $midDesc ?? 0,
            $tech,
            $position,
            $this->_userId
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit
     *
     * Updates a row in the table `experience` and related i18n entries.
     *
     * @throws \Exception
     */
    public function edit(
        int $experienceId = 0,
        array $nameArr = [],
        array $startArr = [],
        array $endArr = [],
        array $companyArr = [],
        array $locationArr = [],
        array $descArr = [],
        string $tech = ''
    ): int {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($experienceId);

        // Name
        foreach ($nameArr as $languageId => $name) {
            $sidName = $i18nModel->editSmall($raw->sid_name, $name, $languageId);
        }

        // Start
        foreach ($startArr as $languageId => $start) {
            $sidStart = $i18nModel->editSmall($raw->sid_start, $start, $languageId);
        }

        // End
        foreach ($endArr as $languageId => $end) {
            $sidEnd = $i18nModel->editSmall($raw->sid_end, $end, $languageId);
        }

        // Company
        foreach ($companyArr as $languageId => $company) {
            $sidCompany = $i18nModel->editSmall($raw->sid_company, $company, $languageId);
        }

        // Location
        foreach ($locationArr as $languageId => $location) {
            $sidLocation = $i18nModel->editSmall($raw->sid_location, $location, $languageId);
        }

        // Description
        foreach ($descArr as $languageId => $desc) {
            $midDesc = $i18nModel->editMedium($raw->mid_desc, $desc, $languageId);
        }

        $query = "
        UPDATE `experience` 
        SET 
            `sid_name` = ?,
            `sid_start` = ?,
            `sid_end` = ?,
            `sid_company` = ?,
            `sid_location` = ?,
            `mid_desc` = ?,
            `tech` = ?,
            `update_user_id` = ?
        WHERE `experience_id` = ?
        ";

        $parameters = [
            $sidName ?? 0,
            $sidStart ?? 0,
            $sidEnd ?? 0,
            $sidCompany ?? 0,
            $sidLocation ?? 0,
            $midDesc ?? 0,
            $tech,
            $this->_userId,
            $experienceId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Del
     *
     * Delete a row from the table `experience` and related i18n entries.
     *
     * @throws \Exception
     */
    public function del(int $experienceId = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($experienceId);

        $i18nModel->flushSmall($raw->sid_name);
        $i18nModel->flushSmall($raw->sid_start);
        $i18nModel->flushSmall($raw->sid_end);
        $i18nModel->flushSmall($raw->sid_company);
        $i18nModel->flushSmall($raw->sid_location);
        $i18nModel->flushMedium($raw->mid_desc);

        $query = "DELETE FROM `experience` WHERE `experience_id` = ?";

        $parameters = [$experienceId];

        return $this->delete($query, $parameters);
    }

    /**
     * Get raw
     *
     * Retrieve raw data of an existing record.
     */
    public function getRaw(int $id = 0): object|bool
    {
        $query = "SELECT * FROM `experience` WHERE `experience_id` = ? LIMIT 1";

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
        $fields = $this->describeTableFields('experience');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `experience_id` FROM `experience` WHERE `" . $field . "` = ?";

        $parameters = [$value];

        if ($index) {
            $query .= " AND `experience_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
