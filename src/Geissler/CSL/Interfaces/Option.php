<?php
namespace Geissler\CSL\Interfaces;

/**
 * Additional options for the rendering of citations and bibliographies.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Option
{
    /**
     * Create the options.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml);

    /**
     * Apply the additional options on the rendered data.
     *
     * @param array $data
     * @return array|string
     */
    public function apply(array $data);
}
