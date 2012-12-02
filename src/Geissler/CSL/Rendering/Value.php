<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;

/**
 * Renders the given value.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Value  implements Renderable
{
    /** @var string **/
    private $value;

    /**
     * Parses the variable configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->value =   '';

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'value') {
                $this->value =   (string) $value;
                break;
            }
        }
    }

    /**
     * Renders the variable.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        return $this->value;
    }
}
