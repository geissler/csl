<?php
namespace Geissler\CSL\Rendering;

/**
 * Formats the given text.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Formating implements Interfaces\Renderable
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
     * Parses the formating configuration.
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
    }

    /**
     * Apply the formating.
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
        }
        elseif ($this->align == 'sup') {
            $style[] = 'vertical-align:super';
        }

        if (count($style) > 0) {
            $data = '<font style="' . implode(';', $style) . '">' . $data . '</font>';
        }

        return $data;
    }
}
