<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Interfaces\Parental;
use Geissler\CSL\Choose\Disambiguate;
use Geissler\CSL\Choose\IsNumeric;
use Geissler\CSL\Choose\IsUncertainDate;
use Geissler\CSL\Choose\Locator;
use Geissler\CSL\Choose\Position;
use Geissler\CSL\Choose\Type;
use Geissler\CSL\Choose\Variable;
use Geissler\CSL\Rendering\Children;

/**
 * Choose If selection.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class ChooseIf implements Renderable, Groupable, Chooseable, Parental
{
    /** @var Chooseable **/
    private $validation;
    /** @var array **/
    private $children;

    /**
     * Parses the If configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->children =   array();
        $match  =   'all';

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'match') {
                $match  =   (string) $value;
                break;
            }
        }

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'disambiguate':
                    $this->validation   =   new Disambiguate();
                    break;
                case 'is-numeric':
                    $this->validation   =   new IsNumeric((string) $value, $match);
                    break;
                case 'is-uncertain-date':
                    $this->validation   =   new IsUncertainDate((string) $value, $match);
                    break;
                case 'locator':
                    $this->validation   =   new Locator((string) $value, $match);
                    break;
                case 'position':
                    $this->validation   =   new Position((string) $value, $match);
                    break;
                case 'type':
                    $this->validation   =   new Type((string) $value, $match);
                    break;
                case 'variable':
                    $this->validation   =   new Variable((string) $value, $match);
                    break;
            }
        }

        $children       =   new Children();
        $this->children =   $children->create($xml);
    }

    /**
     * Render all child elements.
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {
        $result =   array();
        foreach ($this->children as $child) {
            $result[]   =   $child->render($data);
        }

        return implode('', $result);
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        if ($this->render('') == '') {
            return true;
        }

        return false;
    }

    /**
     * Tests if the variable is validates.
     *
     * @return boolean
     */
    public function validate()
    {
        if (isset($this->validation) == true) {
            return $this->validation->validate();
        }

        return false;
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
            if (($child instanceof \Geissler\CSL\Interfaces\Parental) == true
                && $child->isAccessingVariable($name) == true) {
                return true;
            }
        }

        return false;
    }
}
