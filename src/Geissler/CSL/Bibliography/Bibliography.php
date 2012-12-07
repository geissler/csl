<?php
namespace Geissler\CSL\Bibliography;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Container;
use Geissler\CSL\Context\Options;

/**
 * .
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Bibliography implements Renderable
{
    /**
     * Parses the Bibliography configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
         // set Bibliography-specific Options
        Container::getContext()->addBibliography('hangingIndent', false);
        Container::getContext()->addBibliography('lineSpacing', 1);
        Container::getContext()->addBibliography('entrySpacing', 1);
        Container::getContext()->addBibliography('subsequentAuthorSubstituteRule', 'complete-all');

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'hanging-indent':
                    Container::getContext()->addBibliography('hangingIndent', (boolean) $value);
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
     * .
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {

    }
}
