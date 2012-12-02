<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Data\Data;

/**
 * Renders the text contents of a variable.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Variable  implements Interfaces\Renderable
{
    /** @var string **/
    private $name;
    /** @var string **/
    private $form;

    /**
     * Parses the variable configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->name =   '';
        $this->form =   '';

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'variable') {
                $this->name =   (string) $value;
            }
            elseif ($name == 'form') {
                $this->form =   (string) $value;
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
            $return =   Data::getVariable($this->name . '-' . $this->form);

            if ($return !== null) {
                return $return;
            }
        }

        return Data::getVariable($this->name);
    }
}
