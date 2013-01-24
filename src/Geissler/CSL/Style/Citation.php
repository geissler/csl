<?php
namespace Geissler\CSL\Style;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Sorting\Sort;
use Geissler\CSL\Rendering\Layout;
use Geissler\CSL\Container;
use Geissler\CSL\Context\Options;

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

        // set CitationItems-specific Options
        Container::getContext()->addCitation('disambiguateAddNames', false);
        Container::getContext()->addCitation('disambiguateAddGivenname', false);
        Container::getContext()->addCitation('givennameDisambiguationRule', 'by-cite');
        Container::getContext()->addCitation('disambiguateAddYearSuffix', false);
        Container::getContext()->addCitation('nearNoteDistance', 5);

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'disambiguate-add-names':
                    Container::getContext()->addCitation('disambiguateAddNames', isBoolean($value));
                    break;
                case 'disambiguate-add-givenname':
                    Container::getContext()->addCitation('disambiguateAddGivenname', isBoolean($value));
                    break;
                case 'givenname-disambiguation-rule':
                    Container::getContext()->addCitation('givennameDisambiguationRule', (string) $value);
                    break;
                case 'disambiguate-add-year-suffix':
                    Container::getContext()->addCitation('disambiguateAddYearSuffix', isBoolean($value));
                    break;
                case 'cite-group-delimiter':
                    Container::getContext()->addCitation('citeGroupDelimiter', (string) $value);
                    break;
                case 'collapse':
                    Container::getContext()->addCitation('collapse', (string) $value);
                    break;
                case 'year-suffix-delimiter':
                    Container::getContext()->addCitation('yearSuffixDelimiter', (string) $value);
                    break;
                case 'after-collapse-delimiter':
                    Container::getContext()->addCitation('afterCollapseDelimiter', (string) $value);
                    break;
                case 'near-note-distance':
                    Container::getContext()->addCitation('nearNoteDistance', (int) $value);
                    break;
            }
        }

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
        if (Container::hasBibliography() == true
            && Container::getBibliography()->sort() == true) {
            Container::getRendered()->clear();
            $result =   $this->layout->render($data);
        }

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
