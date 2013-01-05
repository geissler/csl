<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Modifiable;

/**
 * Formats the given text.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Formatting implements Renderable, Modifiable
{
    /** @var string **/
    private $style;
    /** @var string **/
    private $variant;
    /** @var string **/
    private $weight;
    /** @var string **/
    private $decoration;
    /** @var string **/
    private $align;

    /**
     * Parses the formatting configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->style        =   '';
        $this->variant      =   '';
        $this->weight       =   '';
        $this->decoration   =   '';
        $this->align        =   '';

        $this->modify($xml);
    }

    /**
     * Modifys the configuration.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Rendering\Formatting
     */
    public function modify(\SimpleXMLElement $xml)
    {
        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'font-style':
                    $this->style    =   (string) $value;
                    break;
                case 'font-variant':
                    $this->variant    =   (string) $value;
                    break;
                case 'font-weight':
                    $this->weight    =   (string) $value;
                    break;
                case 'text-decoration':
                    $this->decoration    =   (string) $value;
                    break;
                case 'vertical-align':
                    $this->align    =   (string) $value;
                    break;
            }
        }

        return $this;
    }

    /**
     * Apply the formatting.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        $style = array();

        if ($this->style == 'italic'
            || $this->style == 'oblique') {
                $style[] = 'font-style:' . $this->style;
        }

        if ($this->variant == 'small-caps') {
            $style[] = 'font-variant:small-caps';
        }

        if ($this->weight == 'bold'
            || $this->weight == 'light') {
                $style[] = 'font-weight:' . $this->weight;
        }

        if ($this->decoration == 'underline') {
            $style[] = 'text-decoration:underline';
        }

        if ($this->align == 'sub') {
            $style[] = 'vertical-align:sub';
        } elseif ($this->align == 'sup') {
            $style[] = 'vertical-align:super';
        }

        if (count($style) > 0) {
            $data = '<font style="' . implode(';', $style) . '">' . $data . '</font>';
        }

        return $data;
    }
}
