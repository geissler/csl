<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\RenderableElement;
use Geissler\CSL\Container;

/**
 * Renders the text contents of a variable.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Variable implements RenderableElement
{
    /** @var string * */
    private $name;
    /** @var string * */
    private $form;

    /**
     * Parses the variable configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->name = '';
        $this->form = '';

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'variable') {
                $this->name = (string) $value;
            } elseif ($name == 'form') {
                $this->form = (string) $value;
            }
        }
    }

    /**
     * Renders the variable.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        if ($this->form !== '') {
            $return = Container::getData()->getVariable($this->name . '-' . $this->form);

            if ($return !== null) {
                return $return;
            }
        }

        return Container::getData()->getVariable($this->name);
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        if ($this->render('') === null) {
            return true;
        }

        return false;
    }
}
