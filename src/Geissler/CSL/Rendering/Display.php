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
    /** @var string **/
    private $display;

    /**
     * Parses the affix configuration.
     *
     * @param \SimpleXMLElement $affix
     */
    public function __construct(\SimpleXMLElement $affix)
    {
        $this->display  =   '';

        foreach ($affix->attributes() as $name => $value) {
            if ($name == 'display') {
                $this->display   =   (string) $value;
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
                return '<font style="display:block">' . $data . '</font>';
                break;
            case 'left-margin':
                return $data;
                break;
            case 'right-inline':
                return '<font style="display:inline">' . $data . '</font>';
                break;
            case 'indent':
                return '<font style="text-indent: 0px; padding-left: 45px;">' . $data . '</font>';
                break;
            default:
                return $data;
                break;
        }
    }
}
