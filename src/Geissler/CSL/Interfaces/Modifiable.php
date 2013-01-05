<?php
namespace Geissler\CSL\Interfaces;

use Geissler\CSL\Interfaces\Renderable;

/**
 * Allows the re-configuration of an Renderable.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Modifiable extends Renderable
{
    /**
     * Modifies the configuration of the object by parsing a new \SimpleXMLElement.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Interfaces\Modifiable
     */
    public function modify(\SimpleXMLElement $xml);
}
