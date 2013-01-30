<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Interfaces\Modifiable;
use Geissler\CSL\Rendering\Text;
use Geissler\CSL\Date\Date;
use Geissler\CSL\Rendering\Number;
use Geissler\CSL\Names\Names;
use Geissler\CSL\Container;

/**
 * Substitute.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Substitute implements Groupable, Modifiable
{
    /** @var array **/
    private $renderingElements;

    /**
     * Parses the Substitute configuration.
     *
     * @param \SimpleXMLElement $xml
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
     * Modifies the configuration of the object by parsing a new \SimpleXMLElement.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Interfaces\Modifiable
     */
    public function modify(\SimpleXMLElement $xml)
    {
        foreach ($this->renderingElements as $child) {
            if (($child instanceof Modifiable) == true) {
                $child->modify($xml);
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
                Container::getContext()->getSubstitute()->setValue($return);

                if (($rendering instanceof Names) == true) {
                    $variables  =   $rendering->getVariables();
                    Container::getContext()->getSubstitute()->setVariable($variables[0]);
                }
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
