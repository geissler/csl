<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Modifiable;

/**
 * Display affixs.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Affix implements Renderable, Modifiable
{
    /** @var string **/
    private $prefix;
    /** @var string **/
    private $suffix;

    /**
     * Parses the affix configuration.
     *
     * @param \SimpleXMLElement $affix
     */
    public function __construct(\SimpleXMLElement $affix)
    {
        $this->prefix   =   '';
        $this->suffix   =   '';
        $this->modify($affix);
    }

    /**
     * Modifys the actual affix configuration.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Rendering\Affix
     */
    public function modify(\SimpleXMLElement $xml)
    {
        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'prefix') {
                $this->prefix   =   (string) $value;
            }
            elseif ($name == 'suffix') {
                $this->suffix   =   (string) $value;
            }
        }

        return $this;
    }

    /**
     * Adds the affixes.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        if ($data !== '') {
            $data   =   $this->prefix . $data . $this->suffix;
        }

        return $data;
    }
}
