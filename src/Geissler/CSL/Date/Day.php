<?php
namespace Geissler\CSL\Date;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Modifiable;
use Geissler\CSL\Container;

/**
 * Day.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Day implements Renderable, Modifiable
{
    /** @var string **/
    private $form;

    /**
     * Parses the Day configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $day)
    {
        $this->form =   'numeric';
        $this->modify($day);
    }

    /**
     * Modifys the actual day configuration.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Date\Day
     */
    public function modify(\SimpleXMLElement $xml)
    {
        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'form') {
                $this->form =   (string) $value;
                break;
            }
        }

        return $this;
    }

    /**
     * Render a day.
     *
     * @param string|integer $data
     * @return string
     */
    public function render($data)
    {
        switch ($this->form) {
            case 'numeric':
                return (int) $data;
                break;

            case 'numeric-leading-zeros':
                if ((int) $data < 10) {
                    return '0' . (int) $data;
                }

                return $data;
                break;

            case 'ordinal':
                // Some languages, such as French, only use the "ordinal" form for the first day of the month
                if (Container::getLocale()->getOptions('limit-day-ordinals-to-day-1') === true
                    && (int) $data > 1) {
                        return $data;
                }

                $ordinal = 'ordinal-';
                if ((int) $data < 10) {
                    $ordinal .= '0' . (int) $data;
                }
                else {
                    $ordinal .= $data;
                }

                $locale = Container::getLocale()->getTerms($ordinal);

                if ($locale !== null) {
                    return (int) $data . $locale;
                }

                return (int) $data . Container::getLocale()->getTerms('ordinal');
                break;
        }
    }
}
