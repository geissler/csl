<?php
namespace Geissler\CSL\Date;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Date\Day;
use Geissler\CSL\Date\Month;
use Geissler\CSL\Date\Year;

/**
 * Renders a part of a date.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class DatePart implements Renderable
{
    /** @var string **/
    private $name;
    /** @var Renderable **/
    private $render;
    /** @var Affix **/
    private $affix;
    /** @var Formating **/
    private $formating;
    /** @var TextCase **/
    private $textCase;
    /** @var string **/
    private $delimiter;

    /**
     * Parses the affix configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $date)
    {
        $this->name         =   '';
        $this->delimiter    =   '–';

        $this->formating    =   new Formating($date);
        $this->textCase     =   new TextCase($date);
        $this->affix        =   new Affix($date);

        foreach ($date->attributes() as $name => $value) {
            switch ($name) {
                case 'name':
                    $this->name   =   (string) $value;
                    switch ($this->name) {
                        case 'day':
                            $this->render   =   new Day($date);
                            break;

                        case 'month':
                            $this->render   =   new Month($date);
                            break;

                        case 'year':
                            $this->render   =   new Year($date);
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
        if (isset($data[$this->name]) == false) {
            return '';
        }

        $value   =   $this->render->render($data[$this->name]);
        $value   =   $this->formating->render($value);
        $value   =   $this->textCase->render($value);
        return $this->affix->render($value);
    }
}
