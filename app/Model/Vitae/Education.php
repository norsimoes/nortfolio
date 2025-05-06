<?php

namespace Model\Vitae;

use Lib\MySql;

/**
 * Education
 *
 * Data handler for the MySql table with the same name as this class.
 */
class Education extends MySql
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
     * Retrieve the data object of an education record.
     */
    public function getDataObject(int $educationId = 0): object|bool
    {
        /*
         * Load classes
         */
        $languageModel = new \Model\Core\Language();

        /*
         * Education row
         */
        $rowObj = $this->getById($educationId);

        /*
         * Translation languages
         */
        $languagesArr = $languageModel->getActive();

        $institutionData = [];
        $startData = [];
        $endData = [];
        $courseData = [];
        $descData = [];
        $gradeData = [];

        foreach ($languagesArr as $language) {

            if ($language->language_id == $this->_languageId) continue;

            $institutionData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_institution', $educationId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $startData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_start', $educationId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $endData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_end', $educationId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $courseData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_course', $educationId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];

            $descData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('mid_desc', $educationId, 'i18n__text__medium', 'mid', $language->language_id) ?? '',
            ];

            $gradeData[] = (object) [
                'language_id' => $language->language_id,
                'iso2' => $language->iso2,
                'value' => $this->_getTranslation('sid_grade', $educationId, 'i18n__text__small', 'sid', $language->language_id) ?? '',
            ];
        }

        /*
         * Return data
         */
        return (object) [
            'education_id' => $rowObj->education_id ?? 0,
            'position' => $rowObj->position ?? '',
            'data_i18n_id' => $this->_languageId ?? 0,
            'data_i18n_iso2' => $languageModel->getIso2ById($this->_languageId) ?? '',
            'institution' => $rowObj->institution ?? '',
            'institution_i18n' => $institutionData,
            'start' => $rowObj->start ?? '',
            'start_i18n' => $startData,
            'end' => $rowObj->end ?? '',
            'end_i18n' => $endData,
            'course' => $rowObj->course ?? '',
            'course_i18n' => $courseData,
            'description' => $rowObj->description ?? '',
            'description_i18n' => $descData,
            'grade' => $rowObj->grade ?? '',
            'grade_i18n' => $gradeData
        ];
    }

    /**
     * Get translation
     *
     * Retrieve a translation value in a specific language.
     */
    private function _getTranslation(string $field = '', int $educationId = 0, string $table = '', string $column = '', int $languageId = 0): string
    {
        $query = "
        SELECT `i18nTable`.`value`

        FROM `education`
        
        LEFT JOIN `$table` AS `i18nTable` ON (
            `i18nTable`.`$column` = `education`.`$field`
            AND
            `i18nTable`.`language_id` = ?
        )

        WHERE `education`.`education_id` = ?
        ";

        $parameters = [$languageId, $educationId];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Count
     *
     * Count all available records.
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as total FROM `education`";

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
            `education`.`education_id`,
            `education`.`position`,
            `institution_i18n`.`value` AS 'institution',
            `start_i18n`.`value` AS 'start',
            `end_i18n`.`value` AS 'end',
            `course_i18n`.`value` AS 'course',
            `desc_i18n`.`value` AS 'description',
            `grade_i18n`.`value` AS 'grade'

        FROM `education`

        LEFT JOIN `i18n__text__small` AS `institution_i18n` ON (
            `institution_i18n`.`sid` = `education`.`sid_institution`
            AND
            `institution_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `start_i18n` ON (
            `start_i18n`.`sid` = `education`.`sid_start`
            AND
            `start_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `end_i18n` ON (
            `end_i18n`.`sid` = `education`.`sid_end`
            AND
            `end_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `course_i18n` ON (
            `course_i18n`.`sid` = `education`.`sid_course`
            AND
            `course_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = `education`.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `grade_i18n` ON (
            `grade_i18n`.`sid` = `education`.`sid_grade`
            AND
            `grade_i18n`.`language_id` = ?
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
                `education`.`education_id` LIKE ?
                OR 
                `institution_i18n`.`value` LIKE ?
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
    public function getById(int $educationId = 0): object|bool
    {
        $query = "
        SELECT
            `education`.`education_id`,
            `education`.`position`,
            `institution_i18n`.`value` AS 'institution',
            `start_i18n`.`value` AS 'start',
            `end_i18n`.`value` AS 'end',
            `course_i18n`.`value` AS 'course',
            `desc_i18n`.`value` AS 'description',
            `grade_i18n`.`value` AS 'grade'

        FROM `education`

        LEFT JOIN `i18n__text__small` AS `institution_i18n` ON (
            `institution_i18n`.`sid` = `education`.`sid_institution`
            AND
            `institution_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `start_i18n` ON (
            `start_i18n`.`sid` = `education`.`sid_start`
            AND
            `start_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `end_i18n` ON (
            `end_i18n`.`sid` = `education`.`sid_end`
            AND
            `end_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `course_i18n` ON (
            `course_i18n`.`sid` = `education`.`sid_course`
            AND
            `course_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = `education`.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__small` AS `grade_i18n` ON (
            `grade_i18n`.`sid` = `education`.`sid_grade`
            AND
            `grade_i18n`.`language_id` = ?
        )

        WHERE `education`.`education_id` = ?

        LIMIT 1
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $this->_languageId,
            $educationId
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get all for select
     *
     * Select all available records for bootstrap select.
     */
    public function getAllForSelect(string $order = 'education_id ASC'): array
    {
        $returnArr = [];

        $query = "
        SELECT
            `education`.`education_id`,
            `course_i18n`.`value` AS 'course'

        FROM `education`

        LEFT JOIN `i18n__text__small` AS `course_i18n` ON (
            `course_i18n`.`sid` = `education`.`sid_course`
            AND
            `course_i18n`.`language_id` = ?
        )

        ORDER BY " . $order . "
        ";

        $parameters = [$this->_languageId];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[$obj->education_id] = $obj->course;

        return $returnArr;
    }

    /**
     * Get position
     *
     * Retrieve the next record position.
     */
    public function getPosition(): int
    {
        $query = "SELECT IFNULL(max(`position`) + 1, 1) AS 'position' FROM `education` LIMIT 1";

        $parameters = [];

        return (int) $this->fetchValue($query, $parameters);
    }

    /**
     * Update position
     *
     * Updates the column position in the table `education`.
     *
     * @throws \Exception
     */
    public function updatePosition(int $educationId = 0, int $position = 0): int
    {
        $query = "UPDATE `education` SET `position` = ? WHERE `education_id` = ?";

        $parameters = [$position, $educationId];

        return $this->update($query, $parameters);
    }

    /**
     * Add
     *
     * Adds a new row to the table `education` and related i18n entries.
     *
     * @throws \Exception
     */
    public function add(
        array $institutionArr = [],
        array $startArr = [],
        array $endArr = [],
        array $courseArr = [],
        array $descArr = [],
        array $gradeArr = [],
        int $position = 0
    ): int {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);

        // Institution
        $sid = $i18nModel->newSid();
        foreach ($institutionArr as $languageId => $institution) {
            $sidInstitution = $i18nModel->addSmall($institution, $languageId, $sid);
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

        // Course
        $sid = $i18nModel->newSid();
        foreach ($courseArr as $languageId => $course) {
            $sidCourse = $i18nModel->addSmall($course, $languageId, $sid);
        }

        // Description
        $mid = $i18nModel->newMid();
        foreach ($descArr as $languageId => $desc) {
            $midDesc = $i18nModel->addMedium($desc, $languageId, $mid);
        }

        // Grade
        $sid = $i18nModel->newSid();
        foreach ($gradeArr as $languageId => $grade) {
            $sidGrade = $i18nModel->addSmall($grade, $languageId, $sid);
        }

        $query = "
        INSERT INTO `education` 
            (`education_id`, `sid_institution`, `sid_start`, `sid_end`, `sid_course`, `mid_desc`, `sid_grade`, `position`, `creation_user_id`)
        VALUES 
            (NULL, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $parameters = [
            $sidInstitution ?? 0,
            $sidStart ?? 0,
            $sidEnd ?? 0,
            $sidCourse ?? 0,
            $midDesc ?? 0,
            $sidGrade ?? 0,
            $position,
            $this->_userId
        ];

        return $this->insert($query, $parameters);
    }

    /**
     * Edit
     *
     * Updates a row in the table `education` and related i18n entries.
     *
     * @throws \Exception
     */
    public function edit(
        int $educationId = 0,
        array $institutionArr = [],
        array $startArr = [],
        array $endArr = [],
        array $courseArr = [],
        array $descArr = [],
        array $gradeArr = []
    ): int {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($educationId);

        // Institution
        foreach ($institutionArr as $languageId => $institution) {
            $sidInstitution = $i18nModel->editSmall($raw->sid_institution, $institution, $languageId);
        }

        // Start
        foreach ($startArr as $languageId => $start) {
            $sidStart = $i18nModel->editSmall($raw->sid_start, $start, $languageId);
        }

        // End
        foreach ($endArr as $languageId => $end) {
            $sidEnd = $i18nModel->editSmall($raw->sid_end, $end, $languageId);
        }

        // Course
        foreach ($courseArr as $languageId => $course) {
            $sidCourse = $i18nModel->editSmall($raw->sid_course, $course, $languageId);
        }

        // Description
        foreach ($descArr as $languageId => $desc) {
            $midDesc = $i18nModel->editMedium($raw->mid_desc, $desc, $languageId);
        }

        // Grade
        foreach ($gradeArr as $languageId => $grade) {
            $sidGrade = $i18nModel->editSmall($raw->sid_grade, $grade, $languageId);
        }

        $query = "
        UPDATE `education` 
        SET 
            `sid_institution` = ?,
            `sid_start` = ?,
            `sid_end` = ?,
            `sid_course` = ?,
            `mid_desc` = ?,
            `sid_grade` = ?,
            `update_user_id` = ?
        WHERE `education_id` = ?
        ";

        $parameters = [
            $sidInstitution ?? 0,
            $sidStart ?? 0,
            $sidEnd ?? 0,
            $sidCourse ?? 0,
            $midDesc ?? 0,
            $sidGrade ?? 0,
            $this->_userId,
            $educationId
        ];

        return $this->update($query, $parameters);
    }

    /**
     * Del
     *
     * Delete a row from the table `education` and related i18n entries.
     *
     * @throws \Exception
     */
    public function del(int $educationId = 0): int
    {
        /*
         * Handle i18n
         */
        $i18nModel = new \Model\Core\I18n($this->_dbConn);
        $raw = $this->getRaw($educationId);

        $i18nModel->flushSmall($raw->sid_institution);
        $i18nModel->flushSmall($raw->sid_start);
        $i18nModel->flushSmall($raw->sid_end);
        $i18nModel->flushSmall($raw->sid_course);
        $i18nModel->flushMedium($raw->mid_desc);
        $i18nModel->flushSmall($raw->sid_grade);

        $query = "DELETE FROM `education` WHERE `education_id` = ?";

        $parameters = [$educationId];

        return $this->delete($query, $parameters);
    }

    /**
     * Get raw
     *
     * Retrieve raw data of an existing record.
     */
    public function getRaw(int $id = 0): object|bool
    {
        $query = "SELECT * FROM `education` WHERE `education_id` = ? LIMIT 1";

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
        $fields = $this->describeTableFields('education');

        if (!in_array($field, $fields)) {
            throw new \Exception('Trying to use a field that does not exist in this database table.');
        }

        $query = "SELECT `education_id` FROM `education` WHERE `" . $field . "` = ?";

        $parameters = [$value];

        if ($index) {
            $query .= " AND `education_id` != ? ";
            $parameters[] = $index;
        }

        return $this->fetchValue($query, $parameters);
    }

}
