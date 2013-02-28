<?php
/**
 * Compare two multi-dimensional-arrays by each field (numeric keys, starting at 0) until it is sorted. If not sortable
 * keep the actual order.
 *
 * @param array $first
 * @param array $second
 * @return int
 */
function multiCompare($first, $second)
{
    $position   =   0;
    $return     =   0;

    do {
        if (isset($first[$position]) == true) {
            if (preg_match('/^\-?[1-9][0-9]*$/', $first[$position][0]) == 1
                && preg_match('/^\-?[1-9][0-9]*$/', $second[$position][0]) == 1) {
                // compare as numbers
                $firstNumber   =   $first[$position][0] * 1;
                $secondNumber  =   $second[$position][0] * 1;

                if ($firstNumber == $secondNumber) {
                    $return =   0;
                } elseif ($firstNumber < $secondNumber) {
                    $return =   -1;
                } else {
                    $return =   1;
                }
            } elseif (preg_match('/^([1-9]{4})([a-z]{1,2})$/', $first[$position][0], $matchFirst) == 1
                && preg_match('/^([1-9]{4})([a-z]{1,2})$/', $second[$position][0], $matchSecond) == 1) {
                // compare disambiguated years with suffix
                if ($matchFirst[1] < $matchSecond[1]) {
                    $return =   -1;
                } elseif ($matchFirst[1] > $matchSecond[1]) {
                    $return =   1;
                } elseif ($matchFirst[2] < $matchSecond[2]) {
                    $return =   -1;
                } elseif ($matchFirst[2] > $matchSecond[2]) {
                    $return =   1;
                } else {
                    $return =   0;
                }
            } elseif (strtotime($first[$position][0]) !== false
                || strtotime($second[$position][0]) !== false) {
                // compare as dates

                // catch missing day and month
                if (preg_match('/^[0-9]{4}$/', $first[$position][0]) == 1) {
                    $firstDate  =   new \DateTime();
                    $firstDate->setDate($first[$position][0], 1, 1);
                } else {
                    $firstDate  =   new \DateTime($first[$position][0]);
                }

                if (preg_match('/^[0-9]{4}$/', $second[$position][0]) == 1) {
                    $secondDate  =   new \DateTime();
                    $secondDate->setDate($second[$position][0], 1, 1);
                } else {
                    $secondDate  =   new \DateTime($second[$position][0]);
                }

                if ($firstDate == $secondDate) {
                    $return =   0;
                } elseif ($firstDate < $secondDate) {
                    $return =   -1;
                } else {
                    $return =   1;
                }
            } elseif ($first[$position][0] == ''
                || $second[$position][0] == '') {
                // put empty variables always at the end
                if ($first[$position][0] == '') {
                    return 1;
                } else {
                    return -1;
                }
            } else {
                $return =   strnatcmp(
                    mb_strtolower($first[$position][0]),
                    mb_strtolower($second[$position][0])
                );
            }
        } else {
            break;
        }

        if ($first[$position][2] == 'desc'
            && $return !== 0) {
            return $return * -1;
        }

        $position++;
    } while ($return == 0);

    if ($return == 0) {
        $firstEnd   =   end($first);
        $secondEnd  =   end($second);
        $return     =   $firstEnd[1] < $secondEnd[1] ? -1 : 1;
    }

    return (int) $return;
}
