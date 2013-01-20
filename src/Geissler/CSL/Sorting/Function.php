<?php
/**
 * Sorting function to sort an multi-dimensional-array by each field (numeric keys, starting at 0) until it is sorted.
 *
 * @param array $first
 * @param array $second
 * @return int
 */
function multiSort($first, $second)
{
    $position   =   0;
    $return     =   0;

    while ($return == 0) {
        if (isset($first[$position]) == true) {
            if (is_integer($first[$position]['value']) == true) {
                if ($first[$position]['value'] == $second[$position]['value']) {
                    $return =   0;
                } elseif ($first[$position]['value'] < $second[$position]['value']) {
                    $return =   -1;
                } else {
                    $return =   1;
                }
            } else {
                $return =   strnatcmp(
                    mb_strtolower($first[$position]['value']),
                    mb_strtolower($second[$position]['value'])
                );
            }
        } else {
            break;
        }

        if ($first[$position]['sort'] == 'descending') {
            $return =   $return * -1;
        }

        $position++;
    }

    return (int) $return;
}

/**
 * Stable sorting function, which keeps the order of the entries if the values are identical. Function is copied
 * from http://www.php.net/manual/en/function.usort.php#38827 and written by sreid@sea-to-sky.net.
 *
 * @param $array
 * @param string $compareFunction
 * @see http://www.php.net/manual/en/function.usort.php#38827
 */
function mergesort(&$array, $compareFunction = 'multiSort')
{
    // Arrays of size < 2 require no action.
    if (count($array) < 2) {
        return;
    }

    // Split the array in half
    $halfway    =   count($array) / 2;
    $array1     =   array_slice($array, 0, $halfway);
    $array2     =   array_slice($array, $halfway);

    // Recurse to sort the two halves
    mergesort($array1, $compareFunction);
    mergesort($array2, $compareFunction);

    // If all of $array1 is <= all of $array2, just append them.
    if (call_user_func($compareFunction, end($array1), $array2[0]) < 1) {
        $array = array_merge($array1, $array2);
        return;
    }

    // Merge the two sorted arrays into a single sorted array
    $array = array();
    $ptr1 = $ptr2 = 0;
    while ($ptr1 < count($array1) && $ptr2 < count($array2)) {
        if (call_user_func($compareFunction, $array1[$ptr1], $array2[$ptr2]) < 1) {
            $array[] = $array1[$ptr1++];
        } else {
            $array[] = $array2[$ptr2++];
        }
    }

    // Merge the remainder
    while ($ptr1 < count($array1)) {
        $array[] = $array1[$ptr1++];
    }

    while ($ptr2 < count($array2)) {
        $array[] = $array2[$ptr2++];
    }
    return;
}
