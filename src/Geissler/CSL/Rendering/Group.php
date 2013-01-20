<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Interfaces\Parental;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Display;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Rendering\Text;
use Geissler\CSL\Date\Date;
use Geissler\CSL\Rendering\Number;
use Geissler\CSL\Names\Names;
use Geissler\CSL\Rendering\Label;
use Geissler\CSL\Choose\Choose;

/**
 * Group Element.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Group implements Groupable, Parental
{
    /** @var string **/
    private $delimiter;
    /** @var Affix **/
    private $affix;
    /** @var Display **/
    private $display;
    /** @var Formatting **/
    private $formatting;
    /** @var array **/
    private $children;

    /**
     * Parses the Group configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->delimiter    =   '';
        $this->children     =   array();

        $this->affix        =   new Affix($xml);
        $this->display      =   new Display($xml);
        $this->formatting   =   new Formatting($xml);

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
     * Retrieve the first child element matching the given class name.
     *
     * @param string $class full, namespace aware class name
     * @return object
     */
    public function getChildElement($class)
    {
        foreach ($this->children as $child) {
            if (($child instanceof $class) == true) {
                return $child;
            } elseif (($child instanceof \Geissler\CSL\Interfaces\Parental) == true) {
                $subChild   =   $child->getChildElement($class);

                if ($subChild !== false) {
                    return $subChild;
                }
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
        foreach ($this->children as $child) {
            if (($child instanceof $class) == true
                && ($child instanceof \Geissler\CSL\Interfaces\Modifiable) == true) {
                $child->modify($xml);
                return true;
            } elseif (($child instanceof \Geissler\CSL\Interfaces\Parental) == true) {
                if ($child->modifyChildElement($class, $xml) == true) {
                    return true;
                }
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
        foreach ($this->children as $child) {
            if (($child instanceof \Geissler\CSL\Rendering\Variable) == true
                && $child->getName() == $name) {
                return true;
            } elseif (($child instanceof \Geissler\CSL\Interfaces\Parental) == true
                && $child->isAccessingVariable($name) == true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Renders all entries of the group.
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

        return $this->renderGroup();
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        if ($this->renderGroup() !== '') {
            return false;
        }

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

    /**
     * Render all child elements of the group.
     */
    private function renderGroup()
    {
        $result =   array();
        foreach ($this->children as $element) {
            $rendered   =   $element->render('');
            if ($rendered !== '') {
                $result[] =   $rendered;
            }
        }

        $return =   implode($this->delimiter, $result);
        $return =   preg_replace(
            '/[' . preg_quote($this->delimiter) . '][' . preg_quote($this->delimiter) . ']+/',
            $this->delimiter,
            $return
        );
        $return =   $this->display->render($return);
        $return =   $this->formatting->render($return);
        return $this->affix->render($return);
    }
}
