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

            // use season as month
            if (isset($data['season']) == true) {
                $this->data[0]['month'] =   $this->extractSeason($data['season']);
            }

            if (isset($data['raw']) == true) {
                $this->data[0]['raw']   =   $this->formatRaw($data['raw']);
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
            } else {
                $this->data =   array(
                    array('raw' => $data['raw'])
                );
                return  true;
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

        if (isset($values[1]) == true
            || isset($values['month']) == true) {
            $month  =   isset($values['month']) == true ? $values['month'] : $values[1];

            if (in_array($month, array(1, 2, 3, 4, 5, 5, 6, 7, 8, 9, 10, 11, 12)) == true) {
                $date['month']   =   $month;
            } elseif ($month > 12
                && $month <= 17) {
                $date['month']  =   $this->extractSeason(1);
            }
        }

        if (isset($values[2]) == true) {
            $date['day']   =   $values[2];
        } elseif (isset($values['day']) == true) {
            $date['day']   =   $values['day'];
        }

        return $date;
    }

    /**
     * Return locale season name.
     *
     * @param integer $date
     * @return null|string
     */
    private function extractSeason($date)
    {
        return Container::getLocale()->getTerms('season-0' . $date);
    }

    /**
     * Format a raw date with season(s) and year(s).
     *
     * @param string $date
     * @return string
     */
    private function formatRaw($date)
    {
        preg_match_all('/([0-9]{4})/', $date, $years);
        $years  =   array_unique($years[0]);
        preg_match_all('/([A-z]+)/', $date, $seasons);
        $seasons    =   array_unique($seasons[0]);

        if (count($years) > 1) {
            return $seasons[0] . ' ' . $years[0] . '–' . $seasons[1] . ' ' . $years[1];
        }

        return $seasons[0] . '–' . $seasons[1] . ' ' . $years[0];
    }
}
