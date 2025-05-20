<?php
/**
 * Datatables plugin.
 */

namespace Lib\Plugin;

class DataTables
{
    /**
     * Holds the HTML table attributes.
     * @var array
     */
    private static $_attr = [];

    /**
     * Holds the HTML table headers.
     * @var array
     */
    private static $_headers = [];

    /**
     * Holds <tr> element attributes.
     * @var array
     */
    private static $_trAttr = [];

    /**
     * Holds the HTML table rows.
     * @var array
     */
    private static $_rows = [];

    /**
     * Set Attribute
     *
     * Add a new attribute to be applied to the HTML <table> element.
     * If attribute already exists, appends value to existent one.
     *
     * @param string $attribute
     * @param string $value
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
     * @param string $attribute
     */
    public static function unsetAttr( string $attribute = '' )
    {
        if ( is_array(self::$_attr) && isset(self::$_attr[$attribute]) )
        {
            unset(self::$_attr[$attribute]);
        }
    }

    /**
     * Set Header
     *
     * Add an infinite number of TH elements to the <thead> element.
     *
     * You can supply an array or an object with "content" to set the
     * cell content, and "attribute" to set <th> element attributes.
     *
     * With an array, you can also supply anonymous entries, where the
     * cell content is at position 0 and cell attributes at position 1.
     *
     * If you supply a string, it will be used as the cell content.
     *
     * @param array ...$columns
     */
    public static function setHeaders( ...$columns )
    {
        if ( is_array($columns) && count($columns) >= 1 )
        {
            foreach ( $columns as $col )
            {
                $cellData = self::_cellData($col);

                if ( is_array($cellData) )
                {
                    self::$_headers[] = $cellData;
                }
            }
        }
    }

    public static function setRowAttr( string $attribute = '' )
    {
        self::$_trAttr[] = $attribute;
    }

    /**
     * Set Row
     *
     * Add an infinite number of TD elements that form a <tr> element content.
     *
     * You can supply an array or an object with "content" to set the
     * cell content, and "attribute" to set <th> element attributes.
     *
     * With an array, you can also supply anonymous entries, where the
     * cell content is at position 0 and cell attributes at position 1.
     *
     * If you supply a string, it will be used as the cell content.
     *
     * @param array ...$columns
     */
    public static function setRow( ...$columns )
    {
        if ( is_array($columns) && count($columns) >= 1 )
        {
            foreach ( $columns as $col )
            {
                $cellData = self::_cellData($col);

                if ( is_array($cellData) )
                {
                    self::$_rows[] = $cellData;
                }
            }
        }
    }

    /**
     * Cell Data
     *
     * Handles the table cell data, identifying
     * the content and eventual attributes.
     *
     * @param mixed $col
     * @return NULL|array
     */
    private static function _cellData( $col )
    {
        $content = '';
        $attribute = '';

        if ( is_array($col) )
        {
            if ( isset($col['attribute']) && isset($col['content']) )
            {
                $$content = isset($col['content']) ? $col['content'] : '';
                $attribute = isset($col['attribute']) ? $col['attribute'] : '';
            }
            else
            {
                $content = isset($col[0]) ? $col[0] : '';
                $attribute = isset($col[1]) ? $col[1] : '';
            }
        }
        elseif ( is_object($col) )
        {
            $content = property_exists($col, 'content') ? $col->content : '';
            $attribute = property_exists($col, 'attribute') ? $col->attribute : '';
        }
        elseif ( is_string($col) )
        {
            $content = $col;
        }

        if ( empty($content) )
        {
            return null;
        }

        return array(
                'content'   => $content,
                'attribute' => $attribute
        );
    }

    /**
     * Render
     *
     * Returns the accumulated HTML wrapped
     * within the <table/> element.
     *
     * @param string $attributes Attributes, e.g, class="john-doe"
     * @return string HTML ready to be used.
     */
    public static function render()
    {
        $table = new \Lib\Plugin\Table();

        if ( count(self::$_headers) >= 1 )
        {
            $table->thead();

            $table->tr();

            foreach (self::$_headers as $th)
            {
                $table->th($th['content'], $th['attribute']);
            }

            $table->tr(true);

            $table->thead(true);

            $table->tbody();

            if ( count(self::$_rows) >= 1 )
            {
                foreach (array_chunk(self::$_rows, count(self::$_headers)) as $i => $trColumns)
                {
                    $table->tr( false, (isset(self::$_trAttr[$i]) ? self::$_trAttr[$i] : '') );

                    foreach ($trColumns as $td)
                    {
                        $table->td($td['content'], $td['attribute']);
                    }

                    $table->tr(true);
                }
            }

            $table->tbody(true);
        }

        if ( count(self::$_attr) >= 1 )
        {
            foreach ( self::$_attr as $attr => $value )
            {
                $table->setAttr($attr, $value);
            }
        }

        return $table->render(false);
    }
}
