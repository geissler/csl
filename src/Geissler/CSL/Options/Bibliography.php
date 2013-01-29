<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Interfaces\Option;
use Geissler\CSL\Options\ReferenceGrouping;
use Geissler\CSL\Options\Whitespace;

/**
 * Additional options for bibliographies.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Bibliography implements Option
{
    /** @var \Geissler\CSL\Options\ReferenceGrouping */
    private $referenceGrouping;
    /** @var \Geissler\CSL\Options\Whitespace */
    private $whitespace;

    /**
     * Creates the options from the xml object.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->whitespace           =   new Whitespace($xml);
        $this->referenceGrouping    =   new ReferenceGrouping($xml);
    }

    /**
     * Apply the bibliography specific options.
     *
     * @param array $data
     * @param bool $whitespaceOnly render only the whitespace option and implode resulting array
     * @return array|string
     */
    public function apply(array $data, $whitespaceOnly = false)
    {
        // step 1: Reference grouping
        if ($whitespaceOnly == false) {
            $data   =   $this->referenceGrouping->apply($data);
        }

        // step 2: white space
        $data   =   $this->whitespace->apply($data);

        if ($whitespaceOnly == true
            && is_array($data) == true) {
            return implode('', $data);
        }

        return $data;
    }
}
