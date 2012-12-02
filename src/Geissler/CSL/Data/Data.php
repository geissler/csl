<?php
namespace Geissler\CSL\Data;

/**
 * Static storage for the data to parse.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Data
{
    /** @var array **/
    private static $data;
    /** @var integer **/
    private static $length;
    /** @var integer **/
    private static $position;

    /**
     * Set the data for citation and bibliography.
     *
     * @param string $json JSON array
     * @return boolean
     * @throws \ErrorException
     */
    public static function set($json)
    {
        $data           =   json_decode($json, true);
        self::$length   =   count($data);

        if (self::$length > 0) {
            self::$data     =   $data;
            self::$position =   0;

            return true;
        }

        throw new \ErrorException('No data set!');
    }

    /**
     * Retrieve actual entry.
     *
     * @return array|null
     */
    public static function get()
    {
        if (isset(self::$data[self::$position]) == true) {
            return self::$data[self::$position];
        }

        return null;
    }

    /**
     * Retrieve a variable from the actual entry.
     *
     * @param string $name
     * @return string|null
     */
    public static function getVariable($name)
    {
        if (isset(self::$data[self::$position]) == true
            & array_key_exists($name, self::$data[self::$position]) == true) {
                return self::$data[self::$position][$name];
        }

        return null;
    }

    /**
     * Move position to next entry.
     *
     * @return boolean
     */
    public static function next()
    {
        self::$position++;

        if (self::$position < self::$length) {
            return true;
        }

        return false;
    }
}
