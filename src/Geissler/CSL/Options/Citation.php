<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Interfaces\Option;
use Geissler\CSL\Container;
use Geissler\CSL\Options\Disambiguation\Disambiguation;
use Geissler\CSL\Options\RenderFromIds;
use Geissler\CSL\Options\CiteGrouping;
use Geissler\CSL\Options\CiteCollapsing;

/**
 * Additional options for citations.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Citation implements Option
{
    /** @var \Geissler\CSL\Options\Disambiguation\Disambiguation */
    private $disambiguation;
    /** @var \Geissler\CSL\Options\RenderFromIds */
    private $renderFromIds;
    /** @var \Geissler\CSL\Options\CiteGrouping */
    private $citeGrouping;
    /** @var \Geissler\CSL\Options\CiteCollapsing */
    private $citeCollapsing;

    /**
     * Create the objects for the additional options.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->disambiguation   =   new Disambiguation();
        $this->renderFromIds    =   new RenderFromIds();
        $this->citeGrouping     =   new CiteGrouping();
        $this->citeCollapsing   =   new CiteCollapsing();

        // set standard values for citationItems-specific options
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
                    $this->citeGrouping->setCiteGroupDelimiter((string) $value);
                    $this->citeGrouping->setActive(true);
                    break;
                case 'collapse':
                    $this->citeCollapsing->setCollapse((string) $value);
                    $this->citeGrouping->setActive(true);
                    break;
                case 'year-suffix-delimiter':
                    $this->citeCollapsing->setYearSuffixDelimiter((string) $value);
                    break;
                case 'after-collapse-delimiter':
                    $this->citeCollapsing->setAfterCollapseDelimiter((string) $value);
                    break;
                case 'near-note-distance':
                    Container::getContext()->addCitation('nearNoteDistance', (int) $value);
                    break;
            }
        }
    }

    /**
     * Apply the citation options.
     *
     * @param array $data
     * @return array|string
     */
    public function apply(array $data)
    {
        // step 1: disambiguation
        $data   =   $this->disambiguation->apply($data);

        // step 2: create array from rendered by replacing item-ids
        $data   =   $this->renderFromIds->apply($data);

        // step 3: cite grouping
        $data   =   $this->citeGrouping->apply($data);

        // step 4: cite collapsing or implode and replace
        return $this->citeCollapsing->apply($data);
    }
}
