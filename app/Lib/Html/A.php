<?php
namespace Lib\Html;

/**
 * A
 *
 * HTML "a" element generator.
 */
class A
{
    /**
     * Holds the HTML <table> element attributes.
     * @var array
     */
    private static $_attr = [];

    /**
     * Holds the <a> element inner HTML.
     * @var string
     */
    private static $_content = '';

    /**
     * Set Attribute
     *
     * Add a new attribute to be applied to the HTML <table> element.
     * If attribute already exists, appends value to existent one.
     *
     * @param string $attribute Element attribute name.
     * @param string $value Element attribute value.
     */
    public static function setAttr ( string $attribute = '', string $value = '' )
    {
        if ( ! empty($attribute) )
        {
            if ( ! isset(self::$_attr[$attribute]) )
            {
                self::$_attr[$attribute] = trim($value);
            }
            else
            {
                self::$_attr[$attribute] .= ' ' . trim($value);
            }
        }
    }

    /**
     * Set content
     *
     * Set the <a> element inner HTML.
     *
     * @param string $content
     */
    public static function setContent ( string $content = '' )
    {
        self::$_content = $content;
    }

    /**
     * Render
     *
     * Generates and returns the <a> HTML element.
     *
     * @param bool $keepData Set to true to preserve attributes and content values.
     * @return string HTML ready to be used.
     */
    public static function render ( bool $keepData = false )
    {
        $attributesHtml = '';

        if ( count(self::$_attr) >= 1 )
        {
            foreach ( self::$_attr as $attr => $value )
            {
                $attributesHtml .= $attr . '="' . trim($value) . '" ';
            }
        }

        $html = '<a ' . $attributesHtml . '>'
              . self::$_content
              . '</a>';

        /*
         * Reset existent data
         */
        if ( ! $keepData )
        {
            self::$_attr = [];
            self::$_content = '';
        }

        return $html;
    }
}
