<?php
namespace Geissler\CSL\Macro;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Container;

/**
 * Call a macro.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Call implements Renderable, Groupable
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
}
