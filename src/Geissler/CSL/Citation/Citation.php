<?php
namespace Geissler\CSL\Citation;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Container;
use Geissler\CSL\Context\Options;

/**
 * .
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Citation implements Renderable
{
    /**
     * Parses the Citation configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        // set Citation-specific Options
        Container::getContext()->addCitation('disambiguateAddNames', false);
        Container::getContext()->addCitation('disambiguateAddGivenname', false);
        Container::getContext()->addCitation('givennameDisambiguationRule', 'by-cite');
        Container::getContext()->addCitation('disambiguateAddYearSuffix', false);
        Container::getContext()->addCitation('nearNoteDistance', 5);

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'disambiguate-add-names':
                    Container::getContext()->addCitation('disambiguateAddNames', (boolean) $value);
                    break;
                case 'disambiguate-add-givenname':
                    Container::getContext()->addCitation('disambiguateAddGivenname', (boolean) $value);
                    break;
                case 'givenname-disambiguation-rule':
                    Container::getContext()->addCitation('givennameDisambiguationRule', (string) $value);
                    break;
                case 'disambiguate-add-year-suffix':
                    Container::getContext()->addCitation('disambiguateAddYearSuffix', (boolean) $value);
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
     * .
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {

    }
}
