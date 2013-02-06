<?php
namespace Geissler\CSL\Options\PageRange;

/**
 * Expanded.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Expanded
{
    /** @var string */
    private $splitter = '–';

    /**
     * Abbreviated page ranges are expanded to their non-abbreviated form: 42–45, 321–328, 2787–2816.
     *
     * @param string $value
     * @return string
     */
    public function format($value)
    {
        if (preg_match('/^([0-9]+)[\-| |–]+([0-9]+)$/', $value, $match) == 1) {
            if (strlen($match[2]) < strlen($match[1])) {
                $missing    =   strlen($match[1]) - strlen($match[2]);

                $value      =   $match[1] . $this->splitter . substr($match[1], 0, $missing) . $match[2];
            } else {
                $value  =   $match[1] . $this->splitter . $match[2];
            }
        }

        return $value;
    }
}
