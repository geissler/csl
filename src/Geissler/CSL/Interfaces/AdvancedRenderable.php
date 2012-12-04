<?php
namespace Geissler\CSL\Interfaces;

/**
 *
 * @author Benjamin
 */
interface AdvancedRenderable extends Renderable
{
    /**
     * Parses the configuration.
     *
     * @param \SimpleXMLElement $xml
     * @param array $additional
     */
    public function __construct(\SimpleXMLElement $xml, array $additional);
}
