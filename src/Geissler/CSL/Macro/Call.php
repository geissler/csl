<?php
namespace Geissler\CSL\Macro;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Interfaces\Parental;
use Geissler\CSL\Container;

/**
 * Call a macro.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Call implements Renderable, Groupable, Parental
{
    /** @var string **/
    private $name;

    /**
     * Parses the Call configuration.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name =   (string) $name;
    }

    /**
     * Access the macro.
     *
     * @return \Geissler\CSL\Macro\Macro
     */
    public function getMacro()
    {
        return Container::getMacro($this->name);
    }

    /**
     * Calls the macro and renders it.
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {
        return Container::getMacro($this->name)->render($data);
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        return Container::getMacro($this->name)->hasAccessEmptyVariable();
    }

    /**
     * Retrieve the first child element matching the given class name.
     *
     * @param string $class full, namespace aware class name
     * @return object
     */
    public function getChildElement($class)
    {
        return Container::getMacro($this->name)->getChildElement($class);
    }

    /**
     * Tests if the element or an child element is accessing the variable with the given name.
     *
     * @param string $name
     * @return boolean
     */
    public function isAccessingVariable($name)
    {
        return Container::getMacro($this->name)->isAccessingVariable($name);
    }
}
