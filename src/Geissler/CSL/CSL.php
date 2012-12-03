<?php
namespace Geissler\CSL;

use Geissler\CSL\Factory;
use Geissler\CSL\Container;
use Geissler\CSL\Data\Data;

/**
 * Main class to create citations and/or bibliographys.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class CSL
{
    /**
     * Set a language different from the one configured in the style.
     *
     * @param string $language
     * @return \Geissler\CSL\CSL
     */
    public function registerLocale($language = 'en')
    {
        $locale = Factory::locale();
        $locale->readFile($language);
        Container::setLocale($locale);

        return $this;
    }

    public function registerStyle($style)
    {
        return $this;
    }

    /**
     * Set the data as JSON-Array to create the citation/bibliography from.
     * 
     * @param string $json JSON array
     * @return \Geissler\CSL\CSL
     */
    public function registerData($json)
    {
        $data   =   new Data();
        $data->set($json);
        Container::setData($data);

        return $this;
    }

    public function getCitation()
    {

    }

    public function getBibligraphy()
    {

    }
}
