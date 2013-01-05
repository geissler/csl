<?php
namespace Geissler\CSL\Style;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Layout;
use Geissler\CSL\Sorting\Sort;
use Geissler\CSL\Container;

/**
 * Combines the options for CitationItems and Bibiliography objects.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 * @depracted
 */
abstract class DisplayAbstract implements Renderable
{
    /** @var Layout **/
    private $layout;
    /** @var Sort **/
    private $sort;

    /**
     * Parses the CitationItems configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'layout':
                    $this->layout   =   new Layout($child);
                    break;
                case 'sort':
                    $this->sort =   new Sort($child);
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
        if (isset($this->sort) == true) {
            $this->sort->sort(Container::getContext()->getName());
        }

        return $this->layout->render($data);
    }
}
