<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;

/**
 * Display affixs.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Affix implements Renderable
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

        foreach ($affix->attributes() as $name => $value) {
            if ($name == 'prefix') {
                $this->prefix   =   (string) $value;
            }
            elseif ($name == 'suffix') {
                $this->suffix   =   (string) $value;
            }
        }
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
