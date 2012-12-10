<?php
namespace Geissler\CSL\Context;

use Geissler\CSL\Container;

/**
 * Parses the global and inheritable name options.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Options
{
    /**
     * Parses the global and inheritable name options.
     *
     * @param string $type context type (style, citation or bibliography)
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Context\Options
     */
    public function set($type, \SimpleXMLElement $xml)
    {
        switch ($type) {
            case 'style':
                $method =   'addStyle';
                break;
            case 'citation':
                $method =   'addCitation';
                break;
            case 'bibliography':
                $method =   'addBibliography';
                break;
        }

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                // global options
                case 'initialize-with-hyphen':
                    Container::getContext()->$method('initializeWithHyphen', ($value === 'true' ? true : false));
                    break;
                case 'page-range-format':
                    Container::getContext()->$method('pageRangeFormat', (string) $value);
                    break;
                case 'demote-non-dropping-particle':
                    Container::getContext()->$method('demoteNonDroppingParticle', (string) $value);
                    break;
                // Inheritable Name Options
                case 'and':
                    Container::getContext()->$method('and', (string) $value);
                    break;
                case 'delimiter-precedes-et-al':
                    Container::getContext()->$method('delimiterPrecedesEtAl', (string) $value);
                    break;
                case 'delimiter-precedes-last':
                    Container::getContext()->$method('delimiterPrecedesLast', (string) $value);
                    break;
                case 'et-al-min':
                    Container::getContext()->$method('etAlMin', (string) $value);
                    break;
                case 'et-al-use-first':
                    Container::getContext()->$method('etAlUseFirst', (string) $value);
                    break;
                case 'et-al-subsequent-min':
                    Container::getContext()->$method('etAlSubsequentMin', (string) $value);
                    break;
                case 'et-al-subsequent-use-first':
                    Container::getContext()->$method('etAlSubsequentUseFirst', (string) $value);
                    break;
                case 'et-al-use-last':
                    Container::getContext()->$method('etAlUseLast', (string) $value);
                    break;
                case 'initialize':
                    Container::getContext()->$method('initialize', (string) $value);
                    break;
                case 'initialize-with':
                    Container::getContext()->$method('initializeWith', (string) $value);
                    break;
                case 'name-as-sort-order':
                    Container::getContext()->$method('nameAsSortOrder', (string) $value);
                    break;
                case 'sort-separator':
                    Container::getContext()->$method('sortSeparator', (string) $value);
                    break;
                case 'name-form':
                    Container::getContext()->$method('form', (string) $value);
                    break;
                case 'name-delimiter':
                    Container::getContext()->$method('delimiter', (string) $value);
                    break;
            }
        }

        return $this;
    }
}
