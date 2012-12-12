<?php
namespace Geissler\CSL;

use Geissler\CSL\Factory;
use Geissler\CSL\Locale\Locale;
use Geissler\CSL\Macro\Macro;
use Geissler\CSL\Data\Data;
use Geissler\CSL\Data\Abbreviation;
use Geissler\CSL\Context\Context;
use Geissler\CSL\Style\Citation;
use Geissler\CSL\Style\Bibliography;

/**
 * Stores the diffrent objects created on base of a given Style.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Container
{
    /** @var Context **/
    private static $context;
    /** @var Locale **/
    private static $locale;
    /** @var array **/
    private static $macros = array();
    /** @var Citation **/
    private static $citation;
    /** @var Bibliography **/
    private static $bibliography;
    /** @var Data **/
    private static $data;
    /** @var Abbreviation **/
    private static $abbreviation;

    /**
     * Access the context object.
     *
     * @return \Geissler\CSL\Context\Context
     */
    public static function getContext()
    {
        if (isset(self::$context) == false) {
            self::$context  =   new Context();
        }

        return self::$context;
    }

    /**
     * Set the actual locale object.
     *
     * @param \Geissler\CSL\Locale\Locale $locale
     * @return void
     */
    public static function setLocale(Locale $locale)
    {
        self::$locale   =   $locale;
    }

    /**
     * Access the Locale object, if not set thestandard Locale is loaded.
     *
     * @return Locale
     */
    public static function getLocale()
    {
        if (isset(self::$locale) == false) {
            self::setLocale(Factory::locale());
        }

        return self::$locale;
    }

    /**
     * Store a Macro.
     *
     * @param type $name
     * @param \Geissler\CSL\Macro\Macro $macro
     * @return void
     */
    public static function addMacro($name, Macro $macro)
    {
        self::$macros[$name]    =   $macro;
    }

    /**
     * Retrieve a macro.
     *
     * @param string $name
     * @return \Geissler\CSL\Macro\Macro
     * @throws \ErrorException When no macro with this name exists
     */
    public static function getMacro($name)
    {
        if (isset(self::$macros[$name]) == true) {
            return self::$macros[$name];
        }

        throw new \ErrorException('A macro with the name ' . $name . ' is not registered!');
    }

    /**
     * Sets the Citation object.
     *
     * @param \Geissler\CSL\Style\Citation $citation
     */
    public static function setCitation(Citation $citation)
    {
        self::$citation =   $citation;
    }

    /**
     * Access the Citation object.
     *
     * @return \Geissler\CSL\Style\Citation
     * @throws \ErrorException If no object is injected
     */
    public static function getCitation()
    {
        if (isset(self::$citation) == true) {
            return self::$citation;
        }

        throw new \ErrorException('No citation defined!');
    }

    public static function setBibliography(Bibliography $bibliography)
    {
        self::$bibliography =   $bibliography;
    }

    /**
     * Access the Bibliography object.
     *
     * @return \Geissler\CSL\Style\Bibliography
     * @throws \ErrorException If no object is injected
     */
    public static function getBibliography()
    {
        if (isset(self::$bibliography) == true) {
            return self::$bibliography;
        }

        throw new \ErrorException('No bibliography defined!');
    }

    /**
     * Store the data object.
     *
     * @param \Geissler\CSL\Data\Data $data
     * @return void
     */
    public static function setData(Data $data)
    {
        self::$data = $data;
    }

    /**
     * Access the data object.
     *
     * @return Data
     */
    public static function getData()
    {
        return self::$data;
    }

    public static function setAbbreviation(Abbreviation $abbreviation)
    {
        self::$abbreviation =   $abbreviation;
    }

    /**
     *
     * @return \Geissler\CSL\Data\Abbreviation
     */
    public static function getAbbreviation()
    {
        return self::$abbreviation;
    }

    public static function clear()
    {
        if (isset(self::$context) == true) {
            self::$context  =   new Context();
        }

        if (isset(self::$macros) == true) {
            self::$macros   =   array();
        }
    }
}
