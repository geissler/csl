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
    /** @var Layout * */
    private $layout;
    /** @var Sort * */
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
                    $this->layout = new Layout($child);
                    break;
                case 'sort':
                    $this->sort = new Sort($child);
                    break;
            }
        }

        if (isset($this->layout) == false) {
            throw new \ErrorException('Missing layout!');
        }

        // citation options
        $this->layout->setOptions(new CitationOptions($xml));

        // set global options and inheritable name options
        $options = new Options();
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
        $result = $this->layout->render($data);

        // The assignment of the year-suffixes follows the order of the bibliographies entries,
        // so sort if disambiguation by year-suffixes is needed, sort the data by the bibliography
        // and re-render citations
        if (Container::hasBibliography() == true
            && Container::getContext()->getValue('disambiguateAddYearSuffix', 'citation') === true
            && Container::getContext()->getLastDisambiguation() == 'Geissler\CSL\Options\Disambiguation\AddYearSuffix'
            && Container::getBibliography()->sort() == true
        ) {
            Container::getRendered()->clear();

            // re-render citation
            $result = $this->layout->render($data);
        }

        if (Container::getCitationItem() !== false) {
            // apply additional citation formatting options
            Container::getCitationItem()->moveToFirst();
            if (Container::getCitationItem()->get('noteIndex') !== null
                || Container::getCitationItem()->get('index') !== null
            ) {
                $citation = array();
                $length = count($result);
                $prefix = '..';

                for ($i = 0; $i < $length; $i++) {
                    if ($i + 1 == $length) {
                        $prefix = '>>';
                    }

                    // Therefore, the first character of a citation should preferably be uppercased
                    $citation[] = $prefix . '[' . $i . '] ' . $this->upperCase($result[$i]);
                }

                $return = implode("\n", $citation);
            } else {
                array_walk($result, array($this, 'upperCase'));
                $return = implode("\n", $result);
            }
        } else {
            array_walk($result, array($this, 'upperCase'));
            $return = implode("\n", $result);
        }

        Container::getContext()->leave();
        return $return;
    }

    /**
     * In note styles, a citation is often a sentence by itself. Therefore, the first character of a citation should
     * preferably be uppercased when there is no preceding text in the note.
     *
     * @param string $value
     * @return string
     */
    protected function upperCase(&$value)
    {
        if (Container::getContext()->getValue('class', 'style') == 'note') {
            if (preg_match_all('/([A-z|Ä|ä|Ö|ö|Ü|ü|ß]){2,}/', $value, $match) !== false
                && count($match[0]) == 1
            ) {
                $value = ucfirst($value);
            }
        }

        return $value;
    }
}
