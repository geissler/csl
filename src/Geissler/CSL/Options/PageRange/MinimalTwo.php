<?php
namespace Geissler\CSL\Options\PageRange;

/**
 * MinimalTwo.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class MinimalTwo
{
    /** @var string */
    private $splitter = '–';

    /**
     * As "minimal", but at least two digits are kept in the second number when it has two or more digits long.
     *
     * @param $value
     * @return mixed
     */
    public function format($value)
    {
        if (preg_match('/^([0-9]+)(-|–)([0-9]+)$/', $value, $match) == 1) {
            if (strlen($match[3]) == 1) {
                $value  =   $match[1] . $this->splitter . $match[1][strlen($match[1]) - 2] . $match[3];
            } elseif (strlen($match[3]) == 2) {
                $value  =   $match[1] . $this->splitter . $match[3];
            } else {
                $second =   array();
                $diff   =   strlen($match[1]) - strlen($match[3]);
                $first  =   substr($match[1], $diff, strlen($match[1]));
                $length =   strlen($first);

                for ($i = 0; $i < $length - 2; $i++) {
                    if ($first[$i] !== $match[3][$i]) {
                        $second[]   =   $match[3][$i];
                    }
                }

                $second[]   =   $match[3][$length - 2];
                $second[]   =   $match[3][$length - 1];
                $value      =   $match[1] . $this->splitter . implode($second);
            }
        }

        return $value;
    }
}
