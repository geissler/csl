<?php
namespace Geissler\CSL\Date;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Modifiable;
use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Ordinal;

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
     * @param \SimpleXMLElement $day
     */
    public function __construct(\SimpleXMLElement $day)
    {
        $this->form =   'numeric';
        $this->modify($day);
    }

    /**
     * Modifies the actual day configuration.
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

        switch ($this->form) {
            case 'numeric-leading-zeros':
                if ((int) $data < 10) {
                    return '0' . (int) $data;
                }

                return $data;
                break;
            case 'ordinal':
                return Ordinal::render($data, true);
                break;
            case 'numeric':
            default:
                return (int) $data;
                break;
        }
    }
}
