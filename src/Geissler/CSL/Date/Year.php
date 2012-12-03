<?php
namespace Geissler\CSL\Date;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Container;

/**
 * .
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Year implements Renderable
{
    /** @var string **/
    private $form;

    /**
     * Parses the year configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->form =   'long';

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'form') {
                $this->form =   (string) $value;
                break;
            }
        }
    }

    /**
     * Displays a year.
     *
     * @param string|integer $data
     * @return string
     */
    public function render($data)
    {
        if ($data === '') {
            return $data;
        }

        $data   =   (int) $data;
        if ($data < 0) {
            // The "bc" term (Before Christ) is automatically appended to negative years
            return -1 * $data . Container::getLocale()->getTerms('bc');
        }

        if ($data < 1000) {
            // The "ad" term (Anno Domini) is automatically appended to positive years of less than four digits
            return $data . Container::getLocale()->getTerms('ad');
        }

        if ($this->form == 'short') {
            return mb_substr($data, 2);
        }

        return $data;
    }
}
