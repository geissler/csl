<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\Children;
use Geissler\CSL\Container;

/**
 * Layout.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
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

    /**
     * Renders all child element with the data of all Data entrys.
     *
     * @param mixed $data
     * @return string
     */
    public function render($data)
    {
        $result =   array();

        do {
            $entry   =   array();
            foreach ($this->children as $child) {
                $entry[]   =   $child->render($data);
            }
            $result[]   = implode('', $entry);
        } while (Container::getData()->next() == true);

        $return =   implode($this->delimiter, $result);
        $return =   $this->formating->render($return);
        return $this->affix->render($return);
    }
}
