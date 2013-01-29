<?php
namespace Geissler\CSL\Context;

use Geissler\CSL\Container;

/**
 * Store the values used for the last substitute rendering.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Substitute
{
    /** @var string */
    private $variable;
    /** @var string */
    private $value;

    /**
     * Store a rendered value.
     *
     * @param string $value
     * @return Substitute
     */
    public function setValue($value)
    {
        $this->value    =   $value;
        return $this;
    }

    /**
     * Retrieve the last rendered value.
     *
     * @return string
     */
    public function getValue()
    {
        if (isset($this->value) == true) {
            return $this->value;
        }

        return '';
    }

    /**
     * Store the last accessed variable to render a substitute.
     *
     * @param string $variable
     * @return Substitute
     */
    public function setVariable($variable)
    {
        $this->variable =   $variable;
        return $this;
    }

    /**
     * Retrieve the last used variable.
     *
     * @return string
     */
    public function getVariable()
    {
        if (isset($this->variable) == true) {
            return $this->variable;
        }

        return '';
    }

    /**
     * Clear all stored values.
     *
     * @return Substitute
     */
    public function clear()
    {
        unset($this->variable);
        unset($this->value);

        return $this;
    }
}
