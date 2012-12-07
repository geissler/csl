<?php
namespace Geissler\CSL;

use Geissler\CSL\Factory;
use Geissler\CSL\Container;
use Geissler\CSL\Data\Data;

/**
 * Main class to create citations and/or bibliographys by doing all necessary configurations before.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class CSL
{
    public function citation($style, $json, $language = false)
    {
        $this->registerContext('citation')
             ->registerStyle($style)
             ->registerLocale($language)
             ->registerData($json);

        return Container::getCitation()->render('');
    }

    public function bibligraphy($style, $json, $language = false)
    {
        $this->registerContext('bibliography')
             ->registerStyle($style)
             ->registerLocale($language)
             ->registerData($json);

        return Container::getBibliography()->render('');
    }

    /**
     * Set the display mode (citation or bibliography).
     *
     * @param string $context
     * @return \Geissler\CSL\CSL
     */
    private function registerContext($context)
    {
        Container::getContext()->setName($context);

        return $this;
    }

    /**
     * Parses a style file.
     *
     * @param string $name
     * @return \Geissler\CSL\CSL
     */
    private function registerStyle($name)
    {
        $style  = Factory::style();
        $style->readFile($name);

        return $this;
    }

    /**
     * Set the data as JSON-Array to create the citation/bibliography from.
     *
     * @param string $json JSON array
     * @return \Geissler\CSL\CSL
     */
    private function registerData($json)
    {
        $data   =   new Data();
        $data->set($json);
        Container::setData($data);

        return $this;
    }

    /**
     * Set a language different from the one configured in the style.
     *
     * @param string $language
     * @return \Geissler\CSL\CSL
     */
    public function registerLocale($language)
    {
        if ($language !== false) {
            $locale = Factory::locale();
            $locale->readFile($language);
            Container::setLocale($locale);
        }
        return $this;
    }
}
