<?php
namespace Geissler\CSL\Macro;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Rendering\Children;

/**
 * Macro.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Macro implements Renderable, Groupable
{
    /** @var array **/
    private $children;

    /**
     * Parses the configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $children       =   new Children();
        $this->children =   $children->create($xml);
    }

    /**
     * Render the macro.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        $result =   array();
        foreach ($this->children as $child) {
            $result[]   =   $child->render($data);
        }

        return implode('', $result);
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        foreach ($this->children as $child) {
            if ($child->hasAccessEmptyVariable() == true) {
                return true;
            }
        }

        return false;
    }
}
