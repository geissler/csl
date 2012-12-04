<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Modifiable;

/**
 * Strip-periods.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class StripPeriods implements Renderable, Modifiable
{
    /** @var boolean **/
    private $strip;

    /**
     * Parses the strip period configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->strip    =   false;
        $this->modify($xml);
    }

    /**
     * Modifys the configuration.
     * 
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Rendering\StripPeriods
     */
    public function modify(\SimpleXMLElement $xml)
    {
        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'strip-periods') {
                if ((string) $value == 'true') {
                    $this->strip    =   true;
                }
            }
        }

        return $this;
    }

    /**
     * Strip-periods.
     *
     * @param string $data
     * @return string
     * @todo Implement (http://citationstyles.org/downloads/specification.html#strip-periods)
     */
    public function render($data)
    {
        return $data;
    }
}
