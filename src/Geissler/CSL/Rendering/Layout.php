<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\Text;
use Geissler\CSL\Macro\Macro;

/**
 * Description of Layout
 *
 * @author Benjamin
 */
class Layout implements Interfaces\Renderable
{
    /** @var Affix **/
    private $affix;
    /** @var Formating **/
    private $formating;
    /** @var string **/
    private $delimiter;
    /** @var array **/
    private $children;
    /** @var string **/
    private $type;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->affix        =   new Affix($xml);
        $this->formating    =   new Formating($xml);

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'delimiter') {
                $this->delimiter    =   (string) $value;
            }
        }

        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'text':
                    $this->children[]   =   new Text($child);
                    break;

                case 'macro':
                    $this->children[]   =   new Macro($child);
                    break;
            }
        }
    }

    public function setType($type)
    {
        $this->type =   $type;
        return $this;
    }

    public function render($data)
    {

    }
}
