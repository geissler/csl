<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\RenderableElement;

/**
 * .
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Choose implements RenderableElement
{
    /**
     * Parses the Choose configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {

    }

    /**
     * .
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {

    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        
    }
}
