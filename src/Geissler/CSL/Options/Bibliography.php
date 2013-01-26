<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Interfaces\Option;
use Geissler\CSL\Options\ReferenceGrouping;

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

    /**
     * Creates the options from the xml object.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->referenceGrouping    =   new ReferenceGrouping();
        $this->referenceGrouping->setRule('complete-all');

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'subsequent-author-substitute':
                    $this->referenceGrouping->setValue((string) $value);
                    break;
                case 'subsequent-author-substitute-rule':
                    $this->referenceGrouping->setRule((string) $value);
                    break;
            }
        }
    }

    /**
     * Apply the bibliography specific options.
     *
     * @param array $data
     * @return array|string
     */
    public function apply(array $data)
    {
        // step 1: white space
        // @todo white space options

        // step 2: Reference grouping
        return $this->referenceGrouping->apply($data);
    }
}
