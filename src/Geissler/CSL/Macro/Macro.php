<?php
namespace Geissler\CSL\Macro;

use Geissler\CSL\Interfaces\Groupable;

/**
 * Description of Macro
 *
 * @author Benjamin
 */
class Macro implements Groupable
{
    /**
     * Parses the configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {

    }

    /**
     * Render the element.
     *
     * @param string $data
     * @return string
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
