<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Display;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\Quotes;
use Geissler\CSL\Rendering\StripPeriods;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Rendering\Variable;
use Geissler\CSL\Macro\Macro;
use Geissler\CSL\Rendering\Term;
use Geissler\CSL\Rendering\Value;

/**
 * Description of Text
 *
 * @author Benjamin
 */
class Text implements Groupable
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
    /** @var Renderable **/
    private $render;

    /**
     * Parse the text configuration.
     *
     * @param \SimpleXMLElement $text
     */
    public function __construct(\SimpleXMLElement $text)
    {
        $this->affix        =   new Affix($text);
        $this->display      =   new Display($text);
        $this->formating    =   new Formating($text);
        $this->quotes       =   new Quotes($text);
        $this->stripPeriods =   new StripPeriods($text);
        $this->textCase     =   new TextCase($text);

        foreach ($text->attributes() as $name => $value) {
            switch ($name) {
                case 'variable':
                    $this->render   =   new Variable($text);
                    break;
                case 'macro':
                    $this->render   =   new Macro($text);
                    break;
                case 'term':
                    $this->render   =   new Term($text);
                    break;
                case 'value':
                    $this->render   =   new Value($text);
                    break;
            }
        }
    }

    /**
     * Display text value.
     *
     * @param string|array $data
     * @return string
     */
    public function render($data)
    {
        $data   =   $this->render->render($data);
        $data   =   $this->textCase->render($data);
        $data   =   $this->stripPeriods->render($data);
        $data   =   $this->display->render($data);
        $data   =   $this->quotes->render($data);
        $data   =   $this->formating->render($data);

        return $this->affix->render($data);
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean|null
     */
    public function hasAccessEmptyVariable()
    {
        if (($this->render instanceof Groupable) == true) {
            return $this->render->hasAccessEmptyVariable();
        }

        return null;
    }
}
