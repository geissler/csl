<?php
namespace Geissler\CSL;

use Geissler\CSL\Container;
use Geissler\CSL\Locale\Locale;
use Geissler\CSL\Date\Day;
use Geissler\CSL\Date\Month;
use Geissler\CSL\Date\Year;
use Geissler\CSL\Style\Style;

/**
 * Factory, for creating objects which depend on configuration parameters.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Factory
{
    /** @var array **/
    private static $configuration;

    /**
     * Creates a Locale Objekt and injects the configuration paramters.
     * @return \Geissler\CSL\Locale\Locale
     */
    public static function locale()
    {
        self::loadConfig();
        $locale =   new Locale();
        $locale->setDir(self::$configuration['locale']['dir'])
               ->setFile(self::$configuration['locale']['file'])
               ->setPrimaryDialect(self::$configuration['locale']['dialects']);

        return $locale;
    }

    /**
     * Generats a Style object and injects the dir path.
     * 
     * @return \Geissler\CSL\Style\Style
     */
    public static function style()
    {
        self::loadConfig();

        $style  =   new Style();
        $style->setDir(self::$configuration['styles']['dir']);
        return $style;
    }

    /**
     * Creates a Day object containing the locale day configuration and the given form the xml.
     *
     * @param string $form text or numeric
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Date\Day
     */
    public static function day($form, \SimpleXMLElement $xml)
    {
        $standard   = Container::getLocale()->getDateAsXml($form, 'day');

        if ($standard !== null) {
            $day    =   new Day(new \SimpleXMLElement($standard));
            $day->modify($xml);

            return $day;
        }

        return new Day($xml);
    }

    /**
     * Creates a Month object containing the locale month configuration and the given form the xml.
     *
     * @param string $form text or numeric
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Date\Month
     */
    public static function month($form, \SimpleXMLElement $xml)
    {
        $standard   = Container::getLocale()->getDateAsXml($form, 'month');

        if ($standard !== null) {
            $month    =   new Month(new \SimpleXMLElement($standard));
            return $month->modify($xml);
        }

        return new Month($xml);
    }

    /**
     * Creates a Year object containing the locale year configuration and the given form the xml.
     *
     * @param string $form text or numeric
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Date\Year
     */
    public static function year($form, \SimpleXMLElement $xml)
    {
        $standard   = Container::getLocale()->getDateAsXml($form, 'year');

        if ($standard !== null) {
            $year    =   new Year(new \SimpleXMLElement($standard));
            return $year->modify($xml);
        }

        return new Year($xml);
    }

    /**
     * Parses one time the configuration parameters from the configuration.ini.
     *
     * @throws \ErrorException If the configuration.ini is missing
     */
    private static function loadConfig()
    {
        if (isset(self::$configuration) == false) {
            $file   =   __DIR__ . '/../../../configuration.ini';
            if (file_exists($file) == false) {
                throw new \ErrorException('configuration.ini is missing at ' . $file);
            }

            self::$configuration    = parse_ini_file($file, true);
        }
    }
}
