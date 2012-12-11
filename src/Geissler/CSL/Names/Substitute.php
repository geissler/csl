<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Rendering\Text;
use Geissler\CSL\Date\Date;
use Geissler\CSL\Rendering\Number;
use Geissler\CSL\Names\Names;

/**
 * .
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Substitute implements Groupable
{
    /** @var array **/
    private $renderingElements;

    /**
     * Parses the Substitute configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->renderingElements    =   array();

        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'text':
                    $this->renderingElements[]  =   new Text($child);
                    break;
                case 'date':
                    $this->renderingElements[]  =   new Date($child);
                    break;
                case 'number':
                    $this->renderingElements[]  =   new Number($child);
                    break;
                case 'names':
                    $this->renderingElements[]  =   new Names($child);
                    break;
            }
        }
    }

    /**
     * Returns the result of the first rendering element which returns a non-empty value.
     *
     * @param string|array $data
     * @return string
     */
    public function render($data)
    {
        foreach ($this->renderingElements as $rendering) {
            $return =   $rendering->render($data);

            if ($return != '') {
                return $return;
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
        foreach ($this->renderingElements as $element) {
            if ($element->hasAccessEmptyVariable() == false) {
                return false;
            }
        }

        return true;
    }
}
