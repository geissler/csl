<?php
namespace Geissler\CSL\Options\PageRange;

/**
 * Chicago.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Chicago
{
    /** @var string */
    private $splitter = '–';

    /**
     * Page ranges are abbreviated according to the Chicago Manual of Style-rules.
     *
     * @param string $value
     * @return string
     */
    public function format($value)
    {
        if (preg_match('/^([0-9]+)(-|–)([0-9]+)$/', $value, $match) == 1) {
            $firstLength    =   strlen($match[1]);
            $secondLength   =   strlen($match[3]);

            if ($firstLength <= 2
                || $secondLength > $firstLength) {
                // Use all digits
                if ($firstLength == 1
                    || $secondLength > $firstLength) {
                    return $match[1] . $this->splitter . $match[3];
                } elseif ($firstLength == 2
                    && $secondLength == 1) {
                    return $match[1] . $this->splitter . $match[1][0] . $match[3];
                }
            } elseif (preg_match('/00$/', $match[1]) == 1) {
                // 100 or multiple of 100
                // Use all digits
                if ($firstLength == $secondLength) {
                    return $match[1] . $this->splitter . $match[3];
                } else {
                    return $match[1] . $this->splitter . substr($match[1], 0, $firstLength - $secondLength) . $match[3];
                }
            } elseif (preg_match('/0[1-9]$/', $match[1]) == 1) {
                // 101 through 109 (in multiples of 100)
                // Use changed part only, omitting unneeded zeros
                if ($match[3][$secondLength - 2] == 0) {
                    return $match[1] . $this->splitter . $match[3][$secondLength - 1];
                } else {
                    return $match[1] . $this->splitter . $match[3][$secondLength - 2] . $match[3][$secondLength - 1];
                }
            } elseif ($firstLength >= 4) {
                // If numbers are four digits long and three digits change, use all digits
                $second     =   array();
                $different  =   0;
                for ($i = $secondLength - 1; $i >= 0; $i--) {
                    if ($match[1][$i] !== $match[3][$i]
                        || ($firstLength >= 4
                            && $different >= 3)) {
                        $second[]   =   $match[3][$i];
                        $different++;
                    }
                }

                if ($different >= 3) {
                    for ($i = $firstLength - $secondLength - 1; $i >= 0; $i--) {
                        $second[]   =   $match[1][$i];
                    }
                }

                if (count($second) == 1) {
                    if ($secondLength >= 2) {
                        $second[]   =   $match[3][$secondLength - 2];
                    } else {
                        $second[]   =   $match[1][$firstLength - 2];
                    }
                }

                return $match[1] . $this->splitter . implode('', array_reverse($second));
            } else {
                // Use two digits, or more as needed
                $second     =   array();
                for ($i = $secondLength - 1; $i >= 0; $i--) {
                    if ($match[1][$i] !== $match[3][$i]) {
                        $second[]   =   $match[3][$i];
                    }
                }

                if (count($second) == 1) {
                    if ($secondLength >= 2) {
                        $second[]   =   $match[3][$secondLength - 2];
                    } else {
                        $second[]   =   $match[1][$firstLength - 2];
                    }
                }

                return $match[1] . $this->splitter . implode('', array_reverse($second));
            }
        }

        return $value;
    }
}
