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
     * Modifies the configuration.
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
        $style  =   array();

        if ($this->style !== '') {
            $style[] = 'font-style:' . $this->style . ';';
        }

        if ($this->variant == 'small-caps') {
            $style[] = 'font-variant:small-caps;';
        }

        if ($this->weight == 'bold'
            || $this->weight == 'light') {
            $style[] = 'font-weight:' . $this->weight . ';';
        }

        if ($this->decoration == 'underline') {
            $style[] = 'text-decoration:underline;';
        }

        switch ($this->align) {
            case 'sub':
                $style[] = 'vertical-align:sub';
                break;
            case 'sup':
                $style[] = 'vertical-align:super';
                break;
            case 'baseline':
                $style[]    =   'baseline';
                break;
        }

        if (count($style) > 0) {
            if (count($style) == 1) {
                switch ($style[0]) {
                    case 'vertical-align:super':
                        $data   =   '<sup>' . $data . '</sup>';
                        break;
                    case 'font-style:italic;':
                        $data   =   '<i>' . $this->flipInnerFormatting($data, '<i>', '</i>') . '</i>';
                        break;
                    case 'font-weight:bold;':
                        $data   =   '<b>' . $this->flipInnerFormatting($data, '<b>', '</b>') . '</b>';
                        break;
                    case 'font-weight:normal;':
                    case 'font-style:normal;':
                        $data = '<span style="' . implode('', $style) . '">' . $data . '</span>';
                        break;
                    default:
                        $data = '<span style="' . implode('', $style) . '">' . $data . '</span>';
                        break;
                }
            } else {
                $data = '<span style="' . implode('', $style) . '">' . $data . '</span>';
            }
        }

        return $data;
    }

    /**
     * Change the inner identical formatting to <span style="font-style:normal;">.
     *
     * @param string $data
     * @param string $start
     * @param string $end
     * @return string
     */
    private function flipInnerFormatting($data, $start, $end)
    {
        if (preg_match('/' . preg_quote($start, '/') . '/', $data) == 1
            && preg_match('/' . preg_quote($end, '/') . '/', $data) == 1) {
            return str_replace($start, '<span style="font-style:normal;">', str_replace($end, '</span>', $data));
        }

        return $data;
    }
}
