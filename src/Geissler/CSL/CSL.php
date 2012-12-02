<?php

namespace Geissler\CSL;

use Geissler\CSL\Locale\Locale;

/**
 * Description of CSL
 *
 * @author Benjamin
 */
class CSL
{
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
