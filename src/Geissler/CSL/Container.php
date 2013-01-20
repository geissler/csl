<?php
namespace Geissler\CSL;

use Geissler\CSL\Factory;
use Geissler\CSL\Locale\Locale;
use Geissler\CSL\Macro\Macro;
use Geissler\CSL\Data\Data;
use Geissler\CSL\Data\Abbreviation;
use Geissler\CSL\Data\CitationAbstract;
use Geissler\CSL\Context\Context;
use Geissler\CSL\Style\Citation;
use Geissler\CSL\Style\Bibliography;
use Geissler\CSL\Data\Rendered;

/**
 * Stores the different objects created on base of a given Style.
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
    /** @var CitationAbstract **/
    private static $citationItem;
    /** @var Rendered */
    private static $rendered;

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
     * Access the Locale object, if not set the standard Locale is loaded.
     *
     * @return Locale
     */
    public static function getLocale()
    {
        if (isset(self::$locale) == false) {
            self::setLocale(Factory::locale());
            self::$locale->readFile();
        }

        return self::$locale;
    }

    /**
     * Store a Macro.
     *
     * @param string $name
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
     * Sets the CitationItems object.
     *
     * @param \Geissler\CSL\Style\Citation $citation
     */
    public static function setCitation(Citation $citation)
    {
        self::$citation =   $citation;
    }

    /**
     * Access the CitationItems object.
     *
     * @return \Geissler\CSL\Style\Citation
     * @throws \ErrorException If no object is injected
     */
    public static function getCitation()
    {
        if (isset(self::$citation) == true
            && self::$citation !== null) {
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
        if (self::hasBibliography() == true) {
            return self::$bibliography;
        }

        throw new \ErrorException('No bibliography defined!');
    }

    public static function hasBibliography()
    {
        if (isset(self::$bibliography) == true
            && self::$bibliography !== null) {
            return true;
        }

        return false;
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

    /**
     * Sets the citation data object.
     *
     * @param \Geissler\CSL\Data\CitationAbstract $citationItem
     */
    public static function setCitationItem(CitationAbstract $citationItem)
    {
        self::$citationItem =   $citationItem;
    }

    /**
     * Access the citation items.
     *
     * @return \Geissler\CSL\Data\CitationAbstract|boolean
     */
    public static function getCitationItem()
    {
        if (isset(self::$citationItem) == true
            && self::$citationItem !== null) {
            return self::$citationItem;
        }

        return false;
    }

    /**
     * Access the container with the rendered citations and bibliography entries.
     *
     * @return \Geissler\CSL\Data\Rendered
     */
    public static function getRendered()
    {
        if (isset(self::$rendered) == false) {
            self::$rendered =   new Rendered();
        }

        return self::$rendered;
    }

    public static function getActualId()
    {
        if (self::$context->getName() == 'citation'
            && self::getCitationItem()!== false) {
                return self::getCitationItem()->get('id');
        }

        return self::getData()->getVariable('id');
    }

    /**
     * Reset all internal properties.
     *
     * @return void
     */
    public static function clear()
    {
        self::$context = new Context();
        self::$macros = array();
        self::$citationItem = false;
        self::$context = new Context();
        self::$rendered = new Rendered();
        self::setLocale(Factory::locale());
        self::$locale->readFile();
        self::$bibliography = null;
        self::$citation = null;
    }
}
