<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\RenderableElement;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Display;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\Text;
use Geissler\CSL\Date\Date;
use Geissler\CSL\Rendering\Number;
use Geissler\CSL\Names\Names;
use Geissler\CSL\Rendering\Label;
use Geissler\CSL\Choose\Choose;

/**
 * .
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Group implements RenderableElement
{
    /** @var string **/
    private $delimiter;
    /** @var Affix **/
    private $affix;
    /** @var Display **/
    private $display;
    /** @var Formating **/
    private $formating;
    /** @var array **/
    private $children;

    /**
     * Parses the Groupd configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->delimiter    =   '';
        $this->children     =   array();

        $this->affix        =   new Affix($xml);
        $this->display      =   new Display($xml);
        $this->formating    =   new Formating($xml);

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'delimiter') {
                $this->delimiter    =   (string) $value;
                break;
            }
        }

        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'text':
                    $this->children[]   =   new Text($child);
                    break;
                case 'date':
                    $this->children[]   =   new Date($child);
                    break;
                case 'number':
                    $this->children[]   =   new Number($child);
                    break;
                case 'names':
                    $this->children[]   =   new Names($child);
                    break;
                case 'label':
                    $this->children[]   =   new Label($child);
                    break;
                case 'group':
                    $this->children[]   =   new Group($child);
                    break;
                case 'choose':
                    $this->children[]   =   new Choose($child);
                    break;
            }
        }
    }

    /**
     * .
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {
        // its child elements are suppressed if a) at least one rendering element in cs:group calls
        // a variable (either directly or via a macro), and b) all variables that are called are empty
        if ($this->hasAccessEmptyVariable() == true) {
            return '';
        }

        $result =   array();
        foreach ($this->children as $element) {
            $rendered   =   $element->render('');
            if ($rendered !== '') {
                $result[] =   $rendered;
            }
        }

        $return =   implode($this->delimiter, $result);
        $return =   $this->display->render($return);
        $return =   $this->formating->render($return);
        return $this->affix->render($return);
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        $variables  =   0;
        foreach ($this->children as $element) {
            if ($element->hasAccessEmptyVariable() === false) {
                return false;
            } elseif ($element->hasAccessEmptyVariable() === true) {
                $variables++;
            }
        }

        if ($variables > 0) {
            return true;
        }

        return null;
    }
}
