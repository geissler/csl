<?php
namespace Geissler\CSL\Rendering;

/**
 * Strip-periods.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class StripPeriods implements Interfaces\Renderable
{
    /** @var boolean **/
    private $strip;

    /**
     * Parses the strip period configuration.
     *
     * @param \SimpleXMLElement $affix
     */
    public function __construct(\SimpleXMLElement $affix)
    {
        $this->strip    =   false;

        foreach ($affix->attributes() as $name => $value) {
            if ($name == 'strip-periods') {
                if ((string) $value == 'true') {
                    $this->strip    =   true;
                }
            }
        }
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
