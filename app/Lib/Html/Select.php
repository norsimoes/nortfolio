<?php

namespace Lib\Html;

/**
 * Select
 *
 * HTML <select> element generator.
 */
class Select
{
    private static array $_attr = [];
    private static string $_content = '';

    /**
     * Set attr
     *
     * Add a new attribute to be applied to the HTML <select> element.
     * If attribute already exists, appends value to existent one.
     */
    public static function setAttr(string $attribute = '', mixed $value = ''): void
    {
        if (!empty($attribute)) {

            if (!isset(self::$_attr[$attribute])) {

                self::$_attr[$attribute] = $value;

            } else {

                self::$_attr[$attribute] .= ' ' . $value;
            }
        }
    }

    /**
     * Set content
     *
     * Set the <select> element inner HTML.
     */
    public static function setContent(array $options = [], mixed $selectedValue = ''): void
    {
        foreach ($options as $optValue => $optText) {

            $value = $optValue;
            $text = '';
            $attr = '';

            if (is_array($selectedValue)) {

                $selected = in_array($value, $selectedValue) ? 'selected' : '';

            } else {

                $selected = $value == $selectedValue ? 'selected' : '';
            }

            if (is_array($optText)) {

                $text = isset($optText['text']) ? $optText['text'] : '';

                if (isset($optText['attr']) && is_array($optText['attr'])) {

                    foreach ($optText['attr'] as $attrName => $attrValue) {

                        $attr .= $attrName . '="' . $attrValue . '"';
                    }
                }

            } else {

                $text = $optText;
            }

            self::$_content .= '
            <option value="' . $value . '" ' . $attr . ' ' . $selected . '>
                ' . $text . '
            </option>
            ';
        }
    }

    /**
     * Render
     *
     * Generates and returns the <select> HTML element.
     */
    public static function render(bool $keepData = false): string
    {
        $attributesHtml = '';

        if (count(self::$_attr) >= 1) {

            foreach (self::$_attr as $attr => $value) {

                $attributesHtml .= $attr . '="' . trim($value) . '" ';
            }
        }

        $html = '<select ' . $attributesHtml . '>' . self::$_content . '</select>';

        /*
         * Reset existent data
         */
        if (!$keepData) {

            self::$_attr = [];
            self::$_content = '';
        }

        return $html;
    }

}
