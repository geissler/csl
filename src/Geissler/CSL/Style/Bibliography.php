<?php
namespace Geissler\CSL\Style;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Sorting\Sort;
use Geissler\CSL\Rendering\Layout;
use Geissler\CSL\Container;
use Geissler\CSL\Context\Options;

/**
 * Bibliography.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Bibliography implements Renderable
{
    /** @var Layout **/
    private $layout;
    /** @var Sort **/
    private $sort;

    /**
     * Parses the Bibliography configuration.
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

        // set Bibliography-specific Options
        Container::getContext()->addBibliography('hangingIndent', false);
        Container::getContext()->addBibliography('lineSpacing', 1);
        Container::getContext()->addBibliography('entrySpacing', 1);
        Container::getContext()->addBibliography('subsequentAuthorSubstituteRule', 'complete-all');

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'hanging-indent':
                    Container::getContext()->addBibliography(
                        'hangingIndent',
                        ((string) $value == 'true' ? true : false)
                    );
                    break;
                case 'second-field-align':
                    Container::getContext()->addBibliography('secondFieldAlign', (string) $value);
                    break;
                case 'line-spacing':
                    Container::getContext()->addBibliography('lineSpacing', (integer) $value);
                    break;
                case 'entry-spacing':
                    Container::getContext()->addBibliography('entrySpacing', (integer) $value);
                    break;
                case 'subsequent-author-substitute':
                    Container::getContext()->addBibliography('subsequentAuthorSubstitute', (string) $value);
                    break;
                case 'subsequent-author-substitute-rule':
                    Container::getContext()->addBibliography('subsequentAuthorSubstituteRule', (string) $value);
                    break;
            }
        }

         // set global options and inheritable name options
        $options    =   new Options();
        $options->set('bibliography', $xml);
    }

    /**
     * Sort the input data by the rules for bibliographies.
     *
     * @return bool|\Geissler\CSL\Sorting\Sort
     */
    public function sort()
    {
        if (isset($this->sort) == true) {
            $name   =   'bibliography';
            if (Container::getContext()->getName() == 'citation') {
                $name   =   'citation';
            }

            Container::getContext()->setName('bibliography');
            Container::getContext()->enter('bibliography');
            $return =   $this->sort->sort('bibliography');
            Container::getContext()->leave();
            Container::getContext()->setName($name);

            return $return;
        }

        return false;
    }

    /**
     * Render a bibliography.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        // render citation to create year-suffix if necessary
        if (Container::getContext()->getValue('disambiguateAddYearSuffix', 'citation') == true) {
            Container::getContext()->setName('citation');
            Container::getData()->moveToFirst();
            Container::getCitation()->render($data);
            Container::getContext()->setName('bibliography');
        }

        // sort
        $this->sort();

        // render
        Container::getContext()->enter('bibliography');
        $result =   $this->layout->render($data);
        Container::getContext()->leave();

        if (count($result) > 0) {
            return '<div class="csl-bib-body"><div class="csl-entry">'
                . implode('</div><div class="csl-entry">', $this->addOptions($result))
                . '</div></div>';
        }

        return '';
    }

    /**
     * Add additional options for displaying a bibliography.
     *
     * @param array $result
     * @return array
     */
    private function addOptions($result)
    {
        $length =   count($result);

        for ($i = 0; $i < $length; $i++) {
            if (Container::getContext()->getValue('hangingIndent', 'bibliography') == true) {

            } elseif (Container::getContext()->getValue('secondFieldAlign', 'bibliography') == 'flush') {
                $result[$i] =   '<div class="csl-left-margin">'
                    . preg_replace('/( )/', ' </div><div class="csl-right-inline">', $result[$i], 1)
                    . '</div>';
            }
        }

        return $result;
    }
}
