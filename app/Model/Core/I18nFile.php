<?php

namespace Model\Core;

/**
 * I18n file
 *
 * Builds a translation file with data from the database.
 */
class I18nFile
{
    /**
     * Get
     *
     * Builds and returns a normalized i18n array.
     *
     * @throws \Exception
     */
    public function get(string $callSign = ''): array
    {
        $i18nFile = [];

        /*
         * Get translation
         */
        $translationModel = new \Model\Core\Translation();

        $i18nObj = $translationModel->getByCallSign($callSign);

        if (!$i18nObj) throw new \Exception('Translation not found: ' . $callSign);

        /*
         * Get translation groups
         */
        $groupModel = new \Model\Core\TranslationGroup($i18nObj->translation_id);

        $groupArr = $groupModel->getAll();

        if ($groupArr) {

            foreach ($groupArr as $groupObj) {

                $groupCallSign = $groupObj->call_sign;

                $i18nFile[$groupCallSign] = [0 => $groupObj->value];

                /*
                * Get translation items
                */
                $itemModel = new \Model\Core\TranslationItem($i18nObj->translation_id, $groupObj->translation_group_id);

                $itemArr = $itemModel->getAll();

                if ($itemArr) {

                    foreach ($itemArr as $itemObj) {

                        $i18nFile[$groupCallSign][$itemObj->array_key] = $itemObj->value;
                    }
                }
            }
        }

        return $i18nFile;
    }
}
