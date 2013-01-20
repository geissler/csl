<?php
namespace Geissler\CSL\Options\PageRange;

/**
 * Minimal.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Minimal
{
    /** @var string */
    private $splitter = '–';

    /**
     * All digits repeated in the second number are left out.
     *
     * @param string $value
     * @return string mixed
     */
    public function format($value)
    {
        if (preg_match('/^([0-9]+)(-|–)([0-9]+)$/', $value, $match) == 1) {
            if (strlen($match[3]) == 1) {
                $value  =   $match[1] . $this->splitter . $match[3];
            } else {
                $second =   array();
                $diff   =   strlen($match[1]) - strlen($match[3]);
                $first  =   substr($match[1], $diff, strlen($match[1]));
                $length =   strlen($first);

                for ($i = 0; $i < $length; $i++) {
                    if ($first[$i] !== $match[3][$i]) {
                        $second[]   =   $match[3][$i];
                    }
                }

                $value  =   $match[1] . $this->splitter . implode($second);
            }
        }

        return $value;
    }
}
