<?php
namespace Geissler\CSL;

use Geissler\CSL\Locale\Locale;

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
