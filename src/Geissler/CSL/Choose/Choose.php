<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Choose\ChooseIf;
use Geissler\CSL\Choose\ChooseElse;

/**
 * .
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Choose implements Renderable, Groupable
{
    /** @var array **/
    private $children;

    /**
     * Parses the Choose configuration.
     *
     * @param \SimpleXMLElement $date
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
     * .
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {
        foreach ($this->children as $child) {
            if ($child->validate() == true) {
                return $this->render($data);
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
                return $this->hasAccessEmptyVariable();
            }
        }

        return false;
    }
}
