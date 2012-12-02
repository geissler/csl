<?php

namespace Geissler\CSL\Rendering\Interfaces;

/**
 * Defines the methods classes must implement to be renderd.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Renderable
{
    /**
     * Parses the configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml);

    /**
     * Render the element.
     *
     * @param string $data
     * @return string
     */
    public function render($data);
}
