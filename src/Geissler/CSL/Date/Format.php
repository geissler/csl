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
     * @todo raw field support
     */
    public function format($variable)
    {
        $data   =   Container::getData()->getVariable($variable);

        if (isset($data['date-parts']) == true
            && count($data['date-parts']) > 0) {
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
     * Access the well formated date.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
