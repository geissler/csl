<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\Text;
use Geissler\CSL\Macro\Macro;
use Geissler\CSL\Date\Date;
use Geissler\CSL\Rendering\Number;
use Geissler\CSL\Names\Names;

/**
 * Description of Layout
 *
 * @author Benjamin
 */
class Layout implements Renderable
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

    /**
     * Parses the layout configuration.
     *
     * @param \SimpleXMLElement $xml
     */
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
                case 'date':
                    $this->children[]   =   new Date($child);
                    break;
                case 'number':
                    $this->children[]   =   new Number($child);
                    break;
                case 'names':
                    $this->children[]   =   new Names($child);
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
        foreach ($this->children as $child) {
            $data   =   $child->render($data);
        }

        $data   =   $this->formating->render($data);
        $data   =   $this->affix->render($data);

        return $data;
    }
}
