<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Rendering\Text;
use Geissler\CSL\Macro\Macro;
use Geissler\CSL\Date\Date;
use Geissler\CSL\Rendering\Number;
use Geissler\CSL\Names\Names;
use Geissler\CSL\Rendering\Label;
use Geissler\CSL\Rendering\Group;
use Geissler\CSL\Choose\Choose;

/**
 * Parses all children Rendering Elements of the xml object into an array of Renderable objects.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Children
{
    /**
     * Parses all children Rendering Elements of the xml object into an array of Renderable objects.
     *
     * @param \SimpleXMLElement $xml
     * @return array
     */
    public function create(\SimpleXMLElement $xml)
    {
        $children   =   array();

        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'text':
                    $children[] =   new Text($child);
                    break;
                case 'macro':
                    $children[] =   new Macro($child);
                    break;
                case 'date':
                    $children[] =   new Date($child);
                    break;
                case 'number':
                    $children[] =   new Number($child);
                    break;
                case 'names':
                    $children[] =   new Names($child);
                    break;
                case 'label':
                    $children[] =   new Label($child);
                    break;
                case 'group':
                    $children[] =   new Group($child);
                    break;
                case 'choose':
                    $children[] =   new Choose($child);
                    break;
            }
        }

        return $children;
    }
}
