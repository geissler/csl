<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Display;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\Quotes;
use Geissler\CSL\Rendering\StripPeriods;
use Geissler\CSL\Rendering\TextCase;

/**
 * Description of Text
 *
 * @author Benjamin
 */
class Text implements Interfaces\Renderable
{
    /** @var Affix **/
    private $affix;
    /** @var Display **/
    private $display;
    /** @var Formating **/
    private $formating;
    /** @var Quotes **/
    private $quotes;
    /** @var StripPeriods **/
    private $stripPeriods;
    /** @var TextCase **/
    private $textCase;

    private $variable;
    private $macro;
    private $term;
    private $value;

    public function __construct(\SimpleXMLElement $text)
    {
        $this->affix        =   new Affix($text);
        $this->display      =   new Display($text);
        $this->formating    =   new Formating($text);
        $this->quotes       =   new Quotes($text);
        $this->stripPeriods =   new StripPeriods($text);
        $this->textCase     =   new TextCase($text);
    }

    public function render($data)
    {

    }
}
