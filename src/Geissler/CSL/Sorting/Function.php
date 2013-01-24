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
            if (preg_match('/^[1-9][0-9]*$/', $first[$position][0]) == 1) {
                $firstInt   =   (int) $first[$position][0];
                $secondInt  =   (int) $second[$position][0];
                if ($firstInt == $secondInt) {
                    $return =   0;
                } elseif ($firstInt < $secondInt) {
                    $return =   -1;
                } else {
                    $return =   1;
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
