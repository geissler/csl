<?php
namespace Geissler\CSL\Style;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Layout;

/**
 * Combines the options for Citation and Bibiliography objects.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
abstract class DisplayAbstract implements Renderable
{
    /** @var Layout **/
    private $layout;
    /** @var Sort **/
    private $sort;

    /**
     * Parses the Citation configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'layout':
                    $this->layout   =   new Layout($child);
                    break;
                case 'sort':
                    break;
            }
        }
    }

    /**
     * .
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {
        // sort (?)
        return $this->layout->render($data);
    }
}
