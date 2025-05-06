<?php

namespace Lib\Html;

/**
 * Bootstrap Select
 *
 * Bootstrap Select plugin dropdown generator.
 */
class BootstrapSelect
{
    protected bool $_placeholder = true;
    protected static array $_attr = [];

    public array $i18nCore = [];

    /**
     * Class constructor
     */
    public function __construct(bool $addPlaceholder = true)
    {
        $this->_placeholder = $addPlaceholder;

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
    }

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
     * Render
     *
     * Generates the Bootstrap Select HTML using the Select helper class.
     */
    public function render(string $inputName = '', array $options = [], mixed $selectedValue = ''): string
    {
        $select = new \Lib\Html\Select();

        $select->setAttr('class', 'bs-select');
        $select->setAttr('data-show-tic', false);
        $select->setAttr('name', $inputName);

        if (!empty(self::$_attr)) {

            foreach (self::$_attr as $attr => $value) {

                $select->setAttr($attr, $value);
            }
        }

        if ($this->_placeholder) {
            
            $options = ['' => $this->i18nCore['Common'][9]] + $options;
        }

        $select->setContent($options, $selectedValue);

        self::$_attr = [];

        return $select->render();
    }

}
