<?php
namespace Geissler\CSL;

use Geissler\CSL\Factory;
use Geissler\CSL\Container;
use Geissler\CSL\Data\Data;
use Geissler\CSL\Data\Citations;
use Geissler\CSL\Data\CitationItems;
use Geissler\CSL\Data\Abbreviation;

/**
 * Main class to create citations and/or bibliographies by doing all necessary configurations before.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class CSL
{
    /**
     * Set the basic data as JSON-Array to create the citation/bibliography from.
     *
     * @param string $input JSON array
     * @return \Geissler\CSL\CSL
     */
    public function setInput($input)
    {
        if ($input != '') {
            $data   =   new Data();
            $data->set($input);
            Container::setData($data);
        }

        return $this;
    }

    /**
     * Set the style file.
     *
     * @param string $name Name of the CSL style without ending
     * @return \Geissler\CSL\CSL
     */
    public function setStyle($name)
    {
        if ($name != '') {
            $style  = Factory::style();
            $style->readFile(preg_replace('/\.csl$/', '', $name));
        }

        return $this;
    }

    /**
     * Set the Citations.
     *
     * @see https://bitbucket.org/bdarcus/citeproc-test/overview#citations
     * @param string $data JSON array
     * @return CSL
     */
    public function setCitations($data)
    {
        if ($data != '') {
            $citation   =   new Citations();
            $citation->set($data);
            Container::setCitationItem($citation);
        }

        return $this;
    }

    /**
     * Set the Citation-Items.
     *
     * @see https://bitbucket.org/bdarcus/citeproc-test/overview#citation-items
     * @param string $data JSON array
     * @return CSL
     */
    public function setCitationItems($data)
    {
        if ($data != '') {
            $citation   =   new CitationItems();
            $citation->set($data);
            Container::setCitationItem($citation);
        }

        return $this;
    }

    /**
     * Set the Abbreviations.
     *
     * @param string $data JSON array
     * @return CSL
     */
    public function setAbbreviation($data)
    {
        if ($data != '') {
            $abbreviation   =   new Abbreviation();
            $abbreviation->set($data);
            Container::setAbbreviation($abbreviation);
        }

        return $this;
    }

    /**
     * Set a language different from the one configured in the style.
     *
     * @param string $language
     * @return \Geissler\CSL\CSL
     */
    public function changeLocale($language)
    {
        if ($language != '') {
            $locale = Factory::locale();
            $locale->readFile($language);
            Container::setLocale($locale);
        }

        return $this;
    }

    /**
     * Render the citation form the already injected data or from the given values.
     *
     * @param string $style
     * @param string $input
     * @param string $language
     * @return string
     */
    public function citation($style = '', $input = '', $language = '')
    {
        try {
            $this->registerContext('citation')
                 ->setStyle($style)
                 ->changeLocale($language)
                 ->setInput($input);

            return Container::getCitation()->render('');
        } catch (\ErrorException $error) {
            return 'An error occurred! ' . $error->getMessage();
        }
    }

    /**
     * Render the bibliography form the already injected data or from the given values.
     *
     * @param string $style
     * @param string $input
     * @param string $language
     * @return string
     */
    public function bibliography($style = '', $input = '', $language = '')
    {
        try {
            $this->registerContext('bibliography')
                ->setStyle($style)
                ->changeLocale($language)
                ->setInput($input);

            return Container::getBibliography()->render('');
        } catch (\ErrorException $error) {
            return 'An error occurred! ' . $error->getMessage();
        }
    }

    /**
     * Set the display mode (citation or bibliography).
     *
     * @param string $context
     * @return \Geissler\CSL\CSL
     */
    private function registerContext($context)
    {
        Container::clear();
        Container::getContext()->setName($context);

        return $this;
    }
}
