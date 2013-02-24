<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;

/**
 * Display display options.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Display implements Renderable
{
    /** @var string * */
    private $display;

    /**
     * Parses the affix configuration.
     *
     * @param \SimpleXMLElement $affix
     */
    public function __construct(\SimpleXMLElement $affix)
    {
        $this->display = '';

        foreach ($affix->attributes() as $name => $value) {
            if ($name == 'display') {
                $this->display = (string)$value;
            }
        }
    }

    /**
     * Adds the display options.
     *
     * @param string $data
     * @return string
     * @todo Full support of left-margin and right-inline
     * @link http://citationstyles.org/downloads/specification.html#display display
     */
    public function render($data)
    {
        switch ($this->display) {
            case 'block':
                return '<div class="csl-block">' . $data . '</div>';
                break;
            case 'left-margin':
                return '<div class="csl-left-margin">' . $data . '</div>';
                break;
            case 'right-inline':
                return '<div class="csl-right-inline">' . $data . '</div>';
                break;
            case 'indent':
                return '<div class="csl-indent">' . $data . '</div>';
                break;
            default:
                return $data;
                break;
        }
    }
}
