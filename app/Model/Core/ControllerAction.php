<?php

namespace Model\Core;

use Lib\MySql;

/**
 * ControllerAction
 *
 * Data handler for the MySql table with the same name as this class.
 */
class ControllerAction extends MySql
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
     * Retrieve the data object of a controller action record.
     */
    public function getDataObject(string $callSign = ''): ?object
    {
        // Load model class
        $languageModel = new \Model\Core\Language();

        /*
         * Client type row
         */
        $rowObj = $this->getByCallSign($callSign);

        /*
         * Translation languages
         */
        $languagesArr = $languageModel->getActive();

        $nameData = [];
        $descData = [];

        foreach ($languagesArr as $language) {

            $nameData[$language->language_id] = $this->_getTranslation('sid_name', $callSign, 'i18n__text__small', 'sid', $language->language_id);

            if (!$nameData[$language->language_id]) {
                $nameData[$language->language_id] = $this->_getTranslation('sid_name', $callSign, 'i18n__text__small', 'sid', APP_I18N_ID);
            }

            $descData[$language->language_id] = $this->_getTranslation('mid_desc', $callSign, 'i18n__text__medium', 'mid', $language->language_id);

            if (!$descData[$language->language_id]) {
                $descData[$language->language_id] = $this->_getTranslation('mid_desc', $callSign, 'i18n__text__medium', 'mid', APP_I18N_ID);
            }
        }

        /*
         * Return data
         */
        return (object) [
            'controller_action_id' => $rowObj->controller_action_id ?? 0,
            'call_sign' => $rowObj->call_sign ?? 0,
            'name_arr' => $nameData,
            'desc_arr' => $descData
        ];
    }

    /**
     * Get translation
     *
     * Retrieve a translation value in a specific language.
     */
    private function _getTranslation(string $field = '', string $callSign = '', string $table = '', string $column = '', int $languageId = 0): string
    {
        $query = "
        SELECT `i18nTable`.`value`

        FROM `controller_action`
        
        LEFT JOIN `$table` AS `i18nTable` ON (
            `i18nTable`.`$column` = `controller_action`.`$field`
            AND
            `i18nTable`.`language_id` = ?
        )

        WHERE `controller_action`.`call_sign` = ?
        ";

        $parameters = [$languageId, $callSign];

        return $this->fetchValue($query, $parameters);
    }

    /**
     * Get all action
     *
     * Select all action records.
     */
    public function getAllAction(): array
    {
        $query = "
        SELECT 
            `controller_action`.`controller_action_id`,
            `controller_action`.`call_sign`,
            `name_i18n`.`value` AS 'name',
            `desc_i18n`.`value` AS 'desc'

        FROM `controller_action`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `controller_action`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = `controller_action`.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
        )

        WHERE `controller_action`.`is_default` = 0

        ORDER BY `controller_action_id`
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId
        ];

        return $this->fetchAllObject($query, $parameters);
    }

    /**
     * Get all for select
     *
     * Select all records for bootstrap select.
     */
    public function getAllForSelect(): array
    {
        $returnArr = [];

        $query = "
        SELECT 
            `controller_action`.`call_sign`,
            `name_i18n`.`value` AS 'name'

        FROM `controller_action`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `controller_action`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        WHERE `controller_action`.`is_default` = 0

        ORDER BY `controller_action_id`
        ";

        $parameters = [$this->_languageId];

        $arr = $this->fetchAllObject($query, $parameters);

        foreach ($arr as $obj) $returnArr[$obj->name] = $obj->call_sign;

        return $returnArr;
    }

    /**
     * Get by call sign
     *
     * Retrieve a row using the call sign value.
     */
    public function getByCallSign(string $callSign = ''): object|bool
    {
        $query = "
        SELECT 
            `controller_action`.`controller_action_id`,
            `controller_action`.`call_sign`,
            `name_i18n`.`value` AS 'name',
            `desc_i18n`.`value` AS 'desc'

        FROM `controller_action`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `controller_action`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        LEFT JOIN `i18n__text__medium` AS `desc_i18n` ON (
            `desc_i18n`.`mid` = `controller_action`.`mid_desc`
            AND
            `desc_i18n`.`language_id` = ?
        )

        WHERE `controller_action`.`call_sign` = ?

        ORDER BY `controller_action_id`
        ";

        $parameters = [
            $this->_languageId,
            $this->_languageId,
            $callSign
        ];

        return $this->fetchObject($query, $parameters);
    }

    /**
     * Get default action
     *
     * Select the default action record.
     */
    public function getDefaultAction(): string
    {
        $query = "
        SELECT
            `name_i18n`.`value` AS 'name'

        FROM `controller_action`

        LEFT JOIN `i18n__text__small` AS `name_i18n` ON (
            `name_i18n`.`sid` = `controller_action`.`sid_name`
            AND
            `name_i18n`.`language_id` = ?
        )

        WHERE `controller_action`.`is_default` = 1

        LIMIT 1
        ";

        $parameters = [$this->_languageId];

        return $this->fetchValue($query, $parameters);
    }

}
