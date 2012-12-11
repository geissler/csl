<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\Children;

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

    /**
     * Parses the layout configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->delimiter    =   '';

        $this->affix        =   new Affix($xml);
        $this->formating    =   new Formating($xml);

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'delimiter') {
                $this->delimiter    =   (string) $value;
            }
        }

        $children       =   new Children();
        $this->children =   $children->create($xml);
    }


    public function render($data)
    {
        $result =   array();
        foreach ($this->children as $child) {
            $result[]   =   $child->render($data);
        }

        $return =   implode($this->delimiter, $result);
        $return =   $this->formating->render($return);
        $return =   $this->affix->render($return);

        return $return;
    }
}
