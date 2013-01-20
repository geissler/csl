<?php
namespace Geissler\CSL\Helper;

/**
 * Data extraction from arrays.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @licence MIT
 */
class ArrayData
{
    /**
     * Retrieve only the ambiguous entries with there keys from the array.
     *
     * @param array $data
     * @return array
     */
    public static function ambiguous(array $data)
    {
        return self::get($data, true);
    }

    /**
     * Retrieve only the disambiguate entries with there keys from the array.
     * @param array $data
     * @return array
     */
    public static function disambiguate(array $data)
    {
        return self::get($data, false);
    }

    /**
     * Retrieves the ambiguous or disambiguate values.
     *
     * @param array $data
     * @param bool $comparison true = ambiguous entries, false = disambiguate values
     * @return array
     */
    private static function get(array $data, $comparison)
    {
        $ambiguous  =   array_diff_assoc($data, array_unique($data));
        $return     =   array();

        foreach ($data as $id => $value) {
            if (in_array($value, $ambiguous) == $comparison) {
                $return[$id]    =   $value;
            }
        }

        return $return;
    }
}
