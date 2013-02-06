<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Container;

/**
 * Display quotes.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Quotes implements Renderable
{
    /** @var boolean **/
    private $quote;

    /**
     * Parses the quotes configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->quote    =   false;

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'quotes') {
                if ((string) $value == 'true') {
                    $this->quote    =   true;
                }
            }
        }
    }

    /**
     * Adds the quotes.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        if ($this->quote == true) {
            if (strpos($data, Container::getLocale()->getTerms('open-quote')) !== false
                && strpos($data, Container::getLocale()->getTerms('close-quote')) !== false) {

                $masked =   false;
                if (strpos($data, Container::getLocale()->getTerms('open-inner-quote')) !== false
                    && strpos($data, Container::getLocale()->getTerms('close-inner-quote')) !== false) {
                    // masc inner quotes
                    $data   =   str_replace(Container::getLocale()->getTerms('open-inner-quote'), '##', $data);
                    $data   =   str_replace(Container::getLocale()->getTerms('close-inner-quote'), '#', $data);
                    $masked =   true;
                }

                $data   =   str_replace(
                    Container::getLocale()->getTerms('open-quote'),
                    Container::getLocale()->getTerms('open-inner-quote'),
                    $data
                );
                $data   =   str_replace(
                    Container::getLocale()->getTerms('close-quote'),
                    Container::getLocale()->getTerms('close-inner-quote'),
                    $data
                );

                if ($masked == true) {
                    $data   =   str_replace('##', Container::getLocale()->getTerms('open-quote'), $data);
                    $data   =   str_replace('#', Container::getLocale()->getTerms('close-quote'), $data);
                }
            }

            return Container::getLocale()->getTerms('open-quote')
                . $data . Container::getLocale()->getTerms('close-quote');
        }

        return $data;
    }
}
