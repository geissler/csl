<?php
namespace Geissler\CSL\Date;

use Geissler\CSL\Container;

/**
 * Parses the date-values.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Format
{
    /** @var array */
    private $data;

    /**
     * Parses the date-values.
     *
     * @param string $variable Variable name
     * @return boolean
     */
    public function format($variable)
    {
        $data   =   Container::getData()->getVariable($variable);

        if (isset($data['date-parts']) == true
            && count($data['date-parts']) > 0) {
            $this->data =   array();

            foreach ($data['date-parts'] as $values) {
                $this->data[]   =   $this->extractDate($values);
            }

            return true;
        } elseif (is_array($data) == true) {
            if (isset($data['literal']) == true) {
                $this->data =   array($data);
                return true;
            } elseif (isset($data['raw']) == false) {
                $this->data =   array($this->extractDate($data));
                return true;
            } elseif (strtotime($data['raw']) !== false) {
                $date = new \DateTime($data['raw']);
                $this->data =   array(
                    array(
                        'year'  =>  $date->format('Y'),
                        'month' =>  $date->format('m'),
                        'day'   =>  $date->format('d')
                    )
                );

                return true;
            }
        }

        return false;
    }

    /**
     * Access the well formatted date.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Extract the date values into an array.
     *
     * @param array $values
     * @return array
     */
    private function extractDate($values)
    {
        $date   =   array(
            'year'  =>  '',
            'month' =>  '',
            'day'   =>  ''
        );

        if (isset($values[0]) == true) {
            $date['year']   =   $values[0];
        } elseif (isset($values['year']) == true) {
            $date['year']   =   $values['year'];
        }

        if (isset($values[1]) == true) {
            $date['month']   =   $values[1];
        } elseif (isset($values['month']) == true) {
            $date['month']   =   $values['month'];
        }

        if (isset($values[2]) == true) {
            $date['day']   =   $values[2];
        } elseif (isset($values['day']) == true) {
            $date['day']   =   $values['day'];
        }

        return $date;
    }
}
