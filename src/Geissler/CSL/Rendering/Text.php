<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Interfaces\Parental;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Display;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Rendering\Quotes;
use Geissler\CSL\Rendering\StripPeriods;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Rendering\Variable;
use Geissler\CSL\Macro\Call;
use Geissler\CSL\Rendering\Term;
use Geissler\CSL\Rendering\Value;
use Geissler\CSL\Container;

/**
 * Text.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Text implements Renderable, Groupable, Parental
{
    /** @var Affix **/
    private $affix;
    /** @var Display **/
    private $display;
    /** @var Formatting **/
    private $formatting;
    /** @var Quotes **/
    private $quotes;
    /** @var StripPeriods **/
    private $stripPeriods;
    /** @var TextCase **/
    private $textCase;
    /** @var \Geissler\CSL\Interfaces\Renderable **/
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
        $this->formatting   =   new Formatting($text);
        $this->quotes       =   new Quotes($text);
        $this->stripPeriods =   new StripPeriods($text);
        $this->textCase     =   new TextCase($text);

        foreach ($text->attributes() as $name => $value) {
            switch ($name) {
                case 'variable':
                    $this->render   =   new Variable($text);
                    break;
                case 'macro':
                    $this->render   =   new Call((string) $value);
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
     * Retrieve the first child element matching the given class name.
     *
     * @param string $class full, namespace aware class name
     * @return object
     */
    public function getChildElement($class)
    {
        if (($this->render instanceof $class) == true) {
            return $this->render;
        } elseif (($this->render instanceof \Geissler\CSL\Interfaces\Parental) == true) {
            $subChild   =   $this->render->getChildElement($class);

            if ($subChild !== false) {
                return $subChild;
            }
        }

        return false;
    }

    /**
     * Modify the first child element.
     *
     * @param string $class full, namespace aware class name
     * @param \SimpleXMLElement $xml
     * @return boolean
     */
    public function modifyChildElement($class, \SimpleXMLElement $xml)
    {
        if (($this->render instanceof $class) == true
            && ($this->render instanceof \Geissler\CSL\Interfaces\Modifiable) == true) {
            $this->render->modify($xml);
            return true;
        } elseif (($this->render instanceof \Geissler\CSL\Interfaces\Parental) == true) {
            if ($this->render->modifyChildElement($class, $xml) == true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tests if the element or an child element is accessing the variable with the given name.
     *
     * @param string $name
     * @return boolean
     */
    public function isAccessingVariable($name)
    {
        if (($this->render instanceof \Geissler\CSL\Rendering\Variable) == true
            && $this->render->getName() == $name) {
            return true;
        } elseif (($this->render instanceof \Geissler\CSL\Interfaces\Parental) == true) {
            return $this->render->isAccessingVariable($name);
        }

        return false;
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

        // no formatting while sorting
        if (Container::getContext()->in('sort') == true) {
            return $data;
        }

        if ($data !== '') {
            $data   =   $this->textCase->render($data);
            $data   =   $this->stripPeriods->render($data);
            $data   =   $this->display->render($data);
            $data   =   $this->quotes->render($data);
            $data   =   $this->formatting->render($data);
        }

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
