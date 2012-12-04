<?php
namespace Geissler\CSL\Date;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Display;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Date\DatePart;

/**
 * Renders dates.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Date implements Renderable
{
    /** @var Affix **/
    private $affix;
    /** @var Display **/
    private $display;
    /** @var Formating **/
    private $formating;
    /** @var TextCase **/
    private $textCase;
    /** @var string **/
    private $variable;
    /** @var string **/
    private $form;
    /** @var array **/
    private $dateParts;
    /** @var array|string **/
    private $data;

    /**
     * Parses the affix configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $date)
    {
        $this->variable     =   '';
        $this->form         =   '';
        $this->dateParts    =   array();

        $this->affix        =   new Affix($date);
        $this->display      =   new Display($date);
        $this->formating    =   new Formating($date);
        $this->textCase     =   new TextCase($date);

        foreach ($date->attributes() as $name => $value) {
            switch ($name) {
                case 'variable':
                    $this->variable   =   (string) $value;
                    break;
                case 'form':
                    $this->form =   (string) $value;
                    break;
                case 'date-parts':
                    if ($this->form !== '') {
                        $dateParts      =   explode('-', (string) $value);
                        $localeDates    =   Container::getLocale()->getDate($this->form);

                        foreach ($localeDates as $localeDate) {
                            if (in_array($localeDate['name'], $dateParts) == true) {
                                $this->dateParts[]  =   array(
                                            'name'      =>  $localeDate['name'],
                                            'datepart'  =>  new DatePart(
                                                new \SimpleXMLElement($localeDate['xml']),
                                                array('form' => $this->form)
                                            )
                                    );
                            }
                        }
                    }
                    break;
            }
        }

        // load date-part configuration
        if ($this->form !== ''
            && count($this->dateParts) == 0) {

            foreach ($date->attributes() as $name => $value) {
                if ($name == 'date-parts') {
                    $dateParts      =   explode('-', (string) $value);
                    $localeDates    =   Container::getLocale()->getDate($this->form);

                    foreach ($localeDates as $localeDate) {
                        if (in_array($localeDate['name'], $dateParts) == true) {
                            $this->dateParts[]  =   array(
                                        'name'      =>  $localeDate['name'],
                                        'datepart'  =>  new DatePart(
                                            new \SimpleXMLElement($localeDate['xml']),
                                            array('form' => $this->form)
                                        )
                                );
                        }
                    }
                }
            }
        }

        // override locale date configurations with single date-part objects
        $additional =   array('form' => $this->form);
        $length     =   count($this->dateParts);
        foreach ($date->children() as $child) {
            if ($child->getName() == 'date-part') {
                $childName   =   null;
                foreach ($child->attributes() as $name => $value) {
                    if ($name == 'name') {
                        $childName  =   (string) $value;
                    }
                }

                if ($childName !== null) {
                    if ($length == 0) {
                        // configure date with date-parts
                        $this->dateParts[]  =   array(
                                                    'name'      =>  $childName,
                                                    'datepart'  =>  new DatePart($child, $additional));
                    } else {
                        // override standard configuration by modifing the existing date-part configuration
                        for ($i = 0; $i < $length; $i++) {
                            if ($this->dateParts[$i]['name'] == $childName) {
                                $datePartObject =   $this->dateParts[$i]['datepart'];
                                $this->dateParts[$i]['datepart']    =   $datePartObject->modify($child, $additional);
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Adds the display options.
     *
     * @param string $data
     * @return string
     * @todo Full support of left-margin and right-inline
     * @link http://citationstyles.org/downloads/specification.html#display display
     */
    public function render($data)
    {
        if ($this->formatDate() == false) {
            return '';
        }

        if (count($this->data) == 2) {
            // date range
            $result         =   array();
            $delimiter      =   '–';
            $delimiterFrom  =   $this->partWithMaxDiff();

            for ($i = 0; $i < 2; $i++) {
                $return =   array();
                foreach ($this->dateParts as $datePart) {
                    $object     =   $datePart['datepart'];
                    $return[]   =   $object->render($this->data[$i]);

                    // get special delimiter
                    if ($datePart['name'] == $delimiterFrom
                        && $object->getRangeDelimiter() !== '') {

                        $delimiter  =   $object->getRangeDelimiter();
                    }
                }

                $result[]   =   $return;
            }

            // drop equal values
            $length =   count($result[0]);
            for ($i = 0; $i < $length; $i++) {
                if ($result[0][$i] == $result[1][$i]) {
                    $result[0][$i] = '';

                    // trim previous date-parts
                    for ($j = 0; $j < $i; $j++) {
                        $result[0][$j]  = trim($result[0][$j]);
                    }
                }
            }

            $result[0]  = implode('', $result[0]);
            $result[1]  = implode('', $result[1]);

            $value  =   implode($delimiter, $result);
        } else {
            $return =   array();
            foreach ($this->dateParts as $datePart) {
                $object     =   $datePart['datepart'];
                $return[]   =   $object->render($this->data[0]);
            }

            $value =   implode('', $return);
        }

        $value =   $this->affix->render($value);
        $value =   $this->display->render($value);
        $value =   $this->formating->render($value);
        return $this->textCase->render($value);
    }

    /**
     * Parses the date-values.
     * @return boolean
     * @todo raw field support
     */
    private function formatDate()
    {
        $data   =   Container::getData()->getVariable($this->variable);

        if (isset($data['date-parts']) == true) {
            $this->data =   array();

            foreach ($data['date-parts'] as $values) {
                $date   =   array(
                    'year'  =>  '',
                    'month' =>  '',
                    'day'   =>  '');

                if (isset($values[0]) == true) {
                    $date['year']   =   $values[0];
                }

                if (isset($values[1]) == true) {
                    $date['month']   =   $values[1];
                }

                if (isset($values[2]) == true) {
                    $date['day']   =   $values[2];
                }

                $this->data[]   =   $date;
            }

            return true;
        }

        return false;
    }

    /**
     * Returns the name of the date-part with the greates difference.
     *
     * @return string|null
     */
    private function partWithMaxDiff()
    {
        if (count($this->data) == 2) {
            if ($this->data[0]['year'] !== $this->data[1]['year']) {
                return 'year';
            }

            if ($this->data[0]['month'] !== $this->data[1]['month']) {
                return 'month';
            }

            if ($this->data[0]['day'] !== $this->data[1]['day']) {
                return 'day';
            }
        }

        return null;
    }
}
