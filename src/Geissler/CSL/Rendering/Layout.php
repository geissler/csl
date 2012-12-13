<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\Children;
use Geissler\CSL\Rendering\ExpandFormating;
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
    /** @var ExpandFormating **/
    private $expand;
    /** @var array **/
    private $children;

    /**
     * Parses the layout configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->delimiter    =   "\n";

        $this->affix        =   new Affix($xml);
        $this->formating    =   new Formating($xml);
        $this->expand       =   new ExpandFormating();

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
        if (Container::getContext()->getName() == 'citation'
            && Container::getCitationItem() !== false) {
            $result =   $this->citation($data);
        } else {
            $result =   $this->bibliography($data);
        }

        $return =   $this->format(implode($this->delimiter, $result));
        return str_replace('  ', ' ', str_replace('..', '.', $return));
    }

    private function citation($data)
    {
        $result =   array();

        do {
            $group = array();
            do {
                Container::getData()->moveToId(Container::getCitationItem()->get('id'));

                // prefix for citation item
                if (Container::getCitationItem()->get('prefix') !== null) {
                    $group[]    =   Container::getCitationItem()->get('prefix');
                    $group[]    =   ' ';
                }

                foreach ($this->children as $child) {
                    $group[]   =   $child->render($data);
                }

                // suffix for citation item
                if (Container::getCitationItem()->get('suffix') !== null) {
                    $group[]    =   ' ';
                    $group[]    =   Container::getCitationItem()->get('suffix');
                }

            } while (Container::getCitationItem()->nextInGroup() == true);

            $result[]   = implode('', $group);
        } while (Container::getCitationItem()->next() == true);

        return $result;
    }

    private function bibliography($data)
    {
        $result =   array();

        do {
            $entry   =   array();
            foreach ($this->children as $child) {
                $entry[]   =   $child->render($data);
            }
            $result[]   = implode('', $entry);
        } while (Container::getData()->next() == true);

        return $result;
    }

    private function format($data)
    {
        $data =   $this->formating->render($data);
        $data =   $this->expand->render($data);
        return $this->affix->render($data);
    }
}
