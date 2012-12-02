<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;

/**
 * Display quotes.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Quotes implements Renderable
{
    /** @var boolean **/
    private $quote;

    /**
     * Parses the quotes configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->quote    =   false;

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'quotes') {
                if ((string) $value == 'true') {
                    $this->quote    =   true;
                }
            }
        }
    }

    /**
     * Adds the quotes.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        if ($this->quote == true) {
            return '"' . $data . '"';
        }

        return $data;
    }
}
