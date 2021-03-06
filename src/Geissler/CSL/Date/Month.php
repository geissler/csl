<?php
namespace Geissler\CSL\Date;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Modifiable;
use Geissler\CSL\Date\DatePartAbstract;
use Geissler\CSL\Rendering\StripPeriods;
use Geissler\CSL\Container;

/**
 * Month.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 * @todo implement support for seasons http://citationstyles.org/downloads/specification.html#seasons
 */
class Month extends DatePartAbstract implements Renderable, Modifiable
{
    /** @var string **/
    private $form;
    /** @var StripPeriods **/
    private $stripPeriods;

    /**
     * Parses the Month configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        parent::__construct($xml);
        $this->form         =   'long';
        $this->stripPeriods =   new StripPeriods($xml);
        $this->modify($xml);
    }

    /**
     * Modifys the actual month configuration.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Date\Month
     * @todo Create new StripPeriods object is necessary
     */
    public function modify(\SimpleXMLElement $xml)
    {
        $this->stripPeriods->modify($xml);

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'form') {
                $this->form =   (string) $value;
                break;
            }
        }

        return $this;
    }

    /**
     * Renders the month.
     *
     * @param string|integer $data
     * @return string
     * @todo strip periods implementation
     */
    public function render($data)
    {
        if (preg_match('/^([1-9]|0[1-9]|1[0-2])$/', $data) == 0) {
            $data   =   $this->getMonthNumber($data);
        }

        // return season name, if set as month name
        if (preg_match('/^([1-9]|0[1-9]|1[0-2])$/', $data) == 0) {
            return $this->format($data);
        }

        // use always numeric value for sorting
        if (Container::getContext()->in('sort') == true) {
            if ($data !== '') {
                $data = (int) $data;

                if ($data < 10) {
                    return '0' . $data;
                }
                return $data;
            }

            return 00;
        }

        if ($data !== '') {
            switch ($this->form) {
                case 'long':
                    return $this->getLocale($data);
                    break;
                case 'short':
                    return $this->getLocale($data, 'short');
                    break;
                case 'numeric':
                    return $this->format((int) $data);
                    break;
                case 'numeric-leading-zeros':
                    $data = (int) $data;

                    if ($data < 10) {
                        return $this->format('0' . $data);
                    }
                    return $this->format($data);
                    break;
            }
        }

        return $data;
    }

    /**
     * Tries to find the month number to the given month name or abbreviation.
     *
     * @param string $value
     * @return integer|string
     */
    private function getMonthNumber($value)
    {
        $long  =   $this->findMonth($value);
        if ($long > 0) {
            return $long;
        }

        $short  =   $this->findMonth($value, 'short');
        if ($short > 0) {
            return $short;
        }

        return $value;
    }

    /**
     * Compares all locale month entries with the value.
     *
     * @param string $value
     * @param string $form Short or empty
     * @return integer
     */
    private function findMonth($value, $form = '')
    {
        $value  =   mb_strtolower($value);
        for ($i = 1; $i <= 12; $i++) {
            $locale = $this->getLocale($i, $form);
            if (mb_strtolower($locale) == $value) {
                return $i;
            }
        }

        return 0;
    }

    /**
     * Actually reads the locale value from the Locale-Object.
     *
     * @param integer $number
     * @param string $form Short or empty
     * @return string
     */
    private function getLocale($number, $form = '')
    {
        $name   =   'month-';
        if ($number < 10) {
            $name .= '0';
        }

        return $this->format(Container::getLocale()->getTerms($name . (int) $number, $form));
    }
}
