<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Display;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Choose\IsNumeric;
use Geissler\CSL\Rendering\Ordinal;

/**
 * Renders numbers.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Number implements Groupable
{
    /** @var string **/
    private $variable;
    /** @var string **/
    private $form;
    /** @var Affix **/
    private $affix;
    /** @var Display **/
    private $display;
    /** @var Formatting **/
    private $formating;
    /** @var TextCase **/
    private $textCase;

    /**
     * Parses the Number configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->variable =   '';
        $this->form     =   'numeric';

        $this->affix        =   new Affix($xml);
        $this->display      =   new Display($xml);
        $this->formating    =   new Formatting($xml);
        $this->textCase     =   new TextCase($xml);

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'variable') {
                $this->variable =   (string) $value;
            }

            if ($name == 'form') {
                $this->form =   (string) $value;
            }
        }
    }

    /**
     * .
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {
        $variable   =   Container::getData()->getVariable($this->variable);
        $isNumeric  =   new IsNumeric($this->variable);
        if ($isNumeric->validate() == true) {
            $variable   =   $this->formatDelimiter($variable);

            switch ($this->form) {
                case 'numeric':
                    $return =   $variable;
                    break;
                case 'ordinal':
                    $return = Ordinal::render($variable);
                    break;
                case 'long-ordinal':
                    $return = Ordinal::renderLong($variable);
                    break;
                case 'roman':
                    if (preg_match('/[A-z]/', $variable) == 0) {
                        $return =   $this->calcRoman($variable);
                    } else {
                        $return =   $variable;
                    }
                    break;
            }

            $return   =   $this->affix->render($return);
            $return   =   $this->display->render($return);
            $return   =   $this->formating->render($return);
            return $this->textCase->render($return);
        }

        return $variable;
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        if (Container::getData()->getVariable($this->variable) === null) {
            return true;
        }

        return false;
    }

    /**
     * Formats the delimiter.
     *
     * @param string $number
     * @return string
     */
    private function formatDelimiter($number)
    {
        $number = str_replace(' ', '', $number);
        $number = str_replace(',', ', ', $number);
        return str_replace('&', ' & ', $number);
    }

    /**
     * Transfers the arabic number(s) into roman and keeps the delimiter.
     *
     * @param string $number
     * @return string
     */
    private function calcRoman($number)
    {
        $number = str_replace(' ', '', $number);

        preg_match_all('/^([0-9]+)(([\-|,|&])([0-9]+))*$/', $number, $matches);
        $matches[1][0]  =   $this->toRoman($matches[1][0]);
        $matches[4][0]  =   $this->toRoman($matches[4][0]);

        return $this->formatDelimiter($matches[1][0] . $matches[3][0] . $matches[4][0]);
    }

    /**
     * Transfers a arabic number into a roman.
     *
     * @param integer $number
     * @return string
     */
    private function toRoman($number)
    {
        if ($number == '') {
            return $number;
        }

        $numbers    =   array(
            array(),
            array('', 'i', 'ii', 'iii', 'iv', 'v', 'vi', 'vii', 'viii', 'ix'),
            array('', 'x', 'xx', 'xxx', 'xl', 'l', 'lx', 'lxx', 'lxxx', 'xc' ),
            array('', 'c', 'cc', 'ccc', 'cd', 'd', 'dc', 'dcc', 'dccc', 'cm' ),
            array('', 'm', 'mm', 'mmm', 'mmmm', 'mmmmm')
        );

        preg_match_all('/([0-9])/', $number, $positions);
        $length =   count($positions[0]);
        $latin  =   array();

        for ($i = 0; $i < $length; $i++) {
            $latin[]    =   $numbers[$length - $i][$positions[0][$i]];
        }

        return implode('', $latin);
    }
}
