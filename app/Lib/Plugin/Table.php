<?php
/**
 * Table plugin.
 */
namespace Lib\Plugin;

/**
 * Table
 *
 * HTML <table/> generator.
 */
class Table
{
    /**
     * Hold the render output.
     * @var string
     */
    private static $_html = '';

    /**
     * Holds the HTML <table> element attributes.
     * @var array
     */
    private static $_attr = [];

    /**
     * Table Header
     *
     * Adds the HTML <thead> element that defines a set of
     * rows defining the head of the columns of the table.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/thead
     *
     * @param bool $close Set true to render the closing tag.
     */
    public static function thead( bool $close = false )
    {
        self::$_html .= '<' . ($close?'/':'') . 'thead>';
    }

    /**
     * Table Body
     *
     * Adds the HTML <tbody> element that groups one or
     * more <tr> elements as the body of the <table> element.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/tbody
     *
     * @param bool $close Set true to render the closing tag.
     */
    public static function tbody( bool $close = false )
    {
        self::$_html .= '<' . ($close?'/':'') . 'tbody>';
    }

    /**
     * Table Footer
     *
     * Adds the HTML <tfoot> element that defines a set
     * of rows summarizing the columns of the table.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/tfoot
     *
     * @param bool $close Set true to render the closing tag.
     */
    public static function tfoot( bool $close = false )
    {
        self::$_html .= '<' . ($close?'/':'') . 'tfoot>';
    }

    /**
     * Table Row
     *
     * Adds the HTML <tr> element that specifies that the
     * markup contained inside the <tr> block comprises
     * one row of a table.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/tr
     *
     * @param bool $close Set true to render the closing tag.
     */
    public static function tr( bool $close = false, string $attribute = '' )
    {
        if ( ! empty($attribute) )
        {
            self::$_html .= '<tr ' . $attribute . '>';
        }
        else
        {
            self::$_html .= '<' . ($close?'/':'') . 'tr>';
        }
    }

    /**
     * Caption
     *
     * Adds the HTML <caption> element that represents the title of the table.
     * Must be the first descendant of the <table> element.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/caption
     *
     * @param string $content Element inner content (can be anything).
     * @param string $attributes Attributes, e.g, class="john-doe"
     */
    public static function caption( string $content = '', string $attributes = '' )
    {
        self::$_html .= '<caption ' . $attributes . '>' . $content . '</caption>';
    }

    /**
     * Table Heading Cell
     *
     * Adds the HTML <th> element that defines a
     * cell as header of a group of table cells.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/th
     *
     * @param string $content Element inner content (can be anything).
     * @param string $attributes Attributes, e.g, class="john-doe"
     */
    public static function th( string $content = '', string $attributes = '' )
    {
        self::$_html .= '<th ' . $attributes . '>' . $content . '</th>';
    }

    /**
     * Table Cell
     *
     * Adds the HTML <td> element that defines
     * a cell of a table that contains data.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/td
     *
     * @param string $content Element inner content (can be anything).
     * @param string $attributes Attributes, e.g, class="john-doe"
     */
    public static function td( string $content = '', string $attributes = '' )
    {
        self::$_html .= '<td ' . $attributes . '>' . $content . '</td>';
    }

    /**
     * Render
     *
     * Returns the accumulated HTML wrapped
     * within the <table/> element.
     *
     * @param bool $isResponsive If true, <table> element is wrapper with <div class="table-responsive">
     * @return string HTML ready to be used.
     */
    public static function render( bool $isResponsive = false )
    {
        $attributes = '';

        if ( count(self::$_attr) >= 1 )
        {
            foreach ( self::$_attr as $attr => $value )
            {
                $attributes .= $attr . '="' . trim($value) . '" ';
            }
        }

        $html = '';

        if ( $isResponsive )
        {
            $html = '<div class="table-responsive">'
                  . '<table ' . $attributes . '>'
                  . self::$_html
                  . '</table>'
                  . '</div>';
        }
        else
        {
            $html = '<table ' . $attributes . '>'
                  . self::$_html
                  . '</table>';
        }

        return $html;
    }

    /**
     * Set Attribute
     *
     * Add a new attribute to be applied to the HTML <table> element.
     * If attribute already exists, appends value to existent one.
     *
     * @param string $attribute Element attribute name.
     * @param string $value Element attribute value.
     */
    public static function setAttr( string $attribute = '', string $value = '' )
    {
        if ( ! empty($attribute) && ! empty($value) )
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
     * Unset Attribute
     *
     * Remove an existent attribute set to by applied to the HTML <table> element.
     *
     * @param string $attribute Element attribute name.
     */
    public static function unsetAttr( string $attribute = '' )
    {
        if ( is_array(self::$_attr) && isset(self::$_attr[$attribute]) )
        {
            unset(self::$_attr[$attribute]);
        }
    }
}
