<?php
namespace Geissler\CSL\Style;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Sorting\Sort;
use Geissler\CSL\Rendering\Layout;
use Geissler\CSL\Container;
use Geissler\CSL\Context\Options;
use Geissler\CSL\Options\Citation as CitationOptions;

/**
 * Citation.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Citation implements Renderable
{
    /** @var Layout **/
    private $layout;
    /** @var Sort **/
    private $sort;

    /**
     * Parses the CitationItems configuration.
     *
     * @param \SimpleXMLElement $xml
     * @throws \ErrorException If layout is missing
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        // init child elements
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

        if (isset($this->layout) == false) {
            throw new \ErrorException('Missing layout!');
        }

        // citation options
        $this->layout->setOptions(new CitationOptions($xml));

        // set global options and inheritable name options
        $options    =   new Options();
        $options->set('citation', $xml);
    }

    /**
     * Access the citation layout.
     *
     * @return \Geissler\CSL\Rendering\Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Render the citations.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        Container::getContext()->enter('citation');

        // sort
        if (isset($this->sort) == true) {
            $this->sort->sort('citation');
        }

        // render citation
        $result =   $this->layout->render($data);

        // sort bibliography and re-render citations
        /* wrong test file (?)
        if (Container::hasBibliography() == true
            && Container::getBibliography()->sort() == true) {
            Container::getRendered()->clear();
            $result =   $this->layout->render($data);
        }
        */

        if (Container::getCitationItem() !== false) {
            // apply additional citation formatting options
            Container::getCitationItem()->moveToFirst();
            if (Container::getCitationItem()->get('noteIndex') !== null
                || Container::getCitationItem()->get('index') !== null) {
                $citation   =   array();
                $length     =   count($result);
                $prefix     =   '..';

                for ($i = 0; $i < $length; $i++) {
                    if ($i + 1 == $length) {
                        $prefix =   '>>';
                    }

                    $citation[] =   $prefix . '[' . $i . '] ' . $result[$i];
                }

                $return =   implode("\n", $citation);
            } else {
                $return =   implode("\n", $result);
            }
        } else {
            $return =   implode("\n", $result);
        }

        Container::getContext()->leave();
        return $return;
    }
}
