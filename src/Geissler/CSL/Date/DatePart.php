<?php
namespace Geissler\CSL\Date;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Modifiable;
use Geissler\CSL\Factory;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Rendering\Affix;

/**
 * Renders a part of a date.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class DatePart implements Renderable, Modifiable
{
    /** @var string **/
    private $name;
    /** @var Renderable **/
    private $render;
    /** @var Affix **/
    private $affix;
    /** @var Formatting **/
    private $formating;
    /** @var TextCase **/
    private $textCase;
    /** @var string **/
    private $delimiter;

    /**
     * Parses the affix configuration.
     *
     * @param \SimpleXMLElement $date
     * @param array $additional Array with key form and value text or numeric
     */
    public function __construct(\SimpleXMLElement $date, array $additional)
    {
        $this->name         =   '';
        $this->delimiter    =   '–';

        $this->formating    =   new Formatting($date);
        $this->textCase     =   new TextCase($date);
        $this->affix        =   new Affix($date);

        foreach ($date->attributes() as $name => $value) {
            switch ($name) {
                case 'name':
                    $this->name   =   (string) $value;
                    switch ($this->name) {
                        case 'day':
                            $this->render   =   Factory::day($additional['form'], $date);
                            break;
                        case 'month':
                            $this->render   =   Factory::month($additional['form'], $date);
                            break;
                        case 'year':
                            $this->render   =   Factory::year($additional['form'], $date);
                            break;
                    }
                    break;
                case 'range-delimiter':
                    $this->delimiter   =   (string) $value;
                    break;
            }
        }
    }

    /**
     * Modifys the configuration.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Date\DatePart
     */
    public function modify(\SimpleXMLElement $xml)
    {
        $this->formating->modify($xml);
        $this->textCase->modify($xml);
        $this->affix->modify($xml);

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'name':
                    $this->render->modify($xml);
                    break;
                case 'range-delimiter':
                    $this->delimiter   =   (string) $value;
                    break;
            }
        }

        return $this;
    }

    /**
     * Retrieve the range-delimiter.
     *
     * @return string
     */
    public function getRangeDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Renders the date part, if a value is set.
     *
     * @param array $data Array with the keys: month, day, year
     * @return string
     */
    public function render($data)
    {
        if (isset($data[$this->name]) == false
            || $data[$this->name] == '') {
                return '';
        }

        $value   =   $this->render->render($data[$this->name]);
        $value   =   $this->formating->render($value);
        $value   =   $this->textCase->render($value);
        return $this->affix->render($value);
    }
}
