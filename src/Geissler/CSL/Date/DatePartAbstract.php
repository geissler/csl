<?php
namespace Geissler\CSL\Date;

use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Rendering\Affix;

/**
 * Formatting date/date-parts.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
abstract class DatePartAbstract
{
    /** @var \Geissler\CSL\Rendering\Formatting */
    private $formatting;
    /** @var \Geissler\CSL\Rendering\TextCase */
    private $textCase;
    /** @var \Geissler\CSL\Rendering\Affix */
    private $affix;

    /**
     * Parses the formatting configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->formatting   =   new Formatting($xml);
        $this->textCase     =   new TextCase($xml);
        $this->affix        =   new Affix($xml);
    }

    /**
     * Apply the formatting options on a date/date-part element.
     *
     * @param string $value
     * @return string
     */
    protected function format($value)
    {
        $value  =   $this->formatting->render($value);
        $value  =   $this->textCase->render($value);

        // Attributes for affixes are allowed, unless cs:date calls a localized date format
        if (Container::getContext()->get('form', 'date') !== '') {
            return $value;
        }

        return $this->affix->render($value);
    }
}
