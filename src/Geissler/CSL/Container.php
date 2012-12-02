<?php
namespace Geissler\CSL;

use Geissler\CSL\Locale\Locale;
use Geissler\CSL\Macro\Macro;

/**
 * Description of Container
 *
 * @author Benjamin
 */
class Container
{
    /** @var Locale **/
    private static $locale;
    /** @var array **/
    private static $macros;

    public static function setLocale(Locale $locale)
    {
        self::$locale   =   $locale;
    }

    /**
     * Access the Locale object.
     * 
     * @return Locale
     * @throws \ErrorException
     */
    public static function getLocale()
    {
        if (isset(self::$locale) == true) {
            return self::$locale;
        }

        throw new \ErrorException('No locale file set!');
    }

    public static function addMacro($name, Macro $macro)
    {
        if (isset(self::$macros[$name]) == false) {
            self::$macros[$name]    =   $macro;
            return $this;
        }

        throw new \ErrorException('A macro with the name ' . $name . ' is already registered!');
    }

    public static function getMacro($name)
    {
        if (isset(self::$macros[$name]) == false) {
            return self::$macros[$name];
        }

        throw new \ErrorException('A macro with the name ' . $name . ' is not registered!');
    }
}
