<?php
namespace Geissler\CSL\Macro;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Interfaces\Parental;
use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Children;

/**
 * Macro.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Macro implements Renderable, Groupable, Parental
{
    /** @var array **/
    private $children;

    /**
     * Parses the configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $children       =   new Children();
        $this->children =   $children->create($xml);
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
     * Render the macro.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        $result     =   array();
        $sort       =   Container::getContext()->in('sort');
        $hasDate    =   false;

        // drop all non-date values if macro is used in sorting context and macro access one date entry
        if (Container::getContext()->in('sort') == true) {
            foreach ($this->children as $child) {
                if (($child instanceof \Geissler\CSL\Date\Date) == true
                    || ($child instanceof \Geissler\CSL\Date\DatePart) == true) {
                    Container::getContext()->addOption(
                        'sort',
                        'renderJust',
                        array('Geissler\CSL\Date\Date', 'Geissler\CSL\Date\DatePart')
                    );
                    $hasDate    =   true;
                    break;
                } elseif (($child instanceof Parental) == true
                    && ($child->getChildElement('\Geissler\CSL\Date\Date') !== false
                        || $child->getChildElement('\Geissler\CSL\Date\DatePart') !== false)) {
                    Container::getContext()->addOption(
                        'sort',
                        'renderJust',
                        array('Geissler\CSL\Date\Date', 'Geissler\CSL\Date\DatePart')
                    );
                    $hasDate    =   true;
                    break;
                }
            }
        }

        foreach ($this->children as $child) {
            if ($hasDate == false) {
                $result[]   =   $child->render($data);
            } elseif (($child instanceof \Geissler\CSL\Date\Date) == true
                || ($child instanceof \Geissler\CSL\Date\DatePart) == true
                || ($child instanceof Parental) == true) {
                $result[]   =   $child->render($data);
            }
        }

        if ($sort == true) {
            Container::getContext()->removeOption('sort', 'renderJust');
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
        foreach ($this->children as $child) {
            if ($child->hasAccessEmptyVariable() == false) {
                return false;
            }
        }

        return true;
    }
}
