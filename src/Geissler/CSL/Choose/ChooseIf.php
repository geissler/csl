<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Choose\Disambiguate;
use Geissler\CSL\Choose\IsNumeric;
use Geissler\CSL\Choose\IsUncertainDate;
use Geissler\CSL\Choose\Locator;
use Geissler\CSL\Choose\Position;
use Geissler\CSL\Choose\Type;
use Geissler\CSL\Choose\Variable;
use Geissler\CSL\Rendering\Children;

/**
 * .
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class ChooseIf implements Renderable, Groupable, Chooseable
{
    /** @var Chooseable **/
    private $validation;
    /** @var array **/
    private $children;

    /**
     * Parses the If configuration.
     *
     * @param \SimpleXMLElement $date
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
     * .
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
        foreach ($this->children as $child) {
            if ($child->hasAccessEmptyVariable() == true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tests if the variable is numeric.
     *
     * @return boolea
     */
    public function validate()
    {
        return $this->validation->validate();
    }
}
