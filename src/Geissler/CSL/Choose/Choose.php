<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Interfaces\Parental;
use Geissler\CSL\Choose\ChooseIf;
use Geissler\CSL\Choose\ChooseElse;

/**
 * Choose.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Choose implements Renderable, Groupable, Parental
{
    /** @var array **/
    private $children;

    /**
     * Parses the Choose configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->children =   array();

        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'if':
                case 'else-if':
                    $this->children[]   =   new ChooseIf($child);
                    break;
                case 'else':
                    $this->children[]   =   new ChooseElse($child);
                    break;
            }
        }
    }

    /**
     * Render the first child which is validating true.
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {
        foreach ($this->children as $child) {
            if ($child->validate() == true) {
                return $child->render($data);
            }
        }

        return '';
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        foreach ($this->children as $child) {
            if ($child->validate() == true) {
                return $child->hasAccessEmptyVariable();
            }
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
