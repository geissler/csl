<?php
namespace Geissler\CSL\Style;

use Geissler\CSL\Container;
use Geissler\CSL\Factory;
use Geissler\CSL\Context\Options;
use Geissler\CSL\Macro\Macro;
use Geissler\CSL\Style\Citation;
use Geissler\CSL\Style\Bibliography;

/**
 * Reads the style configuration, creates the necessary objects and stores them in the Container.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Style
{
    /** @var string **/
    private $dir;

    /**
     * Sets the path to the style dir.
     *
     * @param string $dir
     * @return \Geissler\CSL\Style\Style
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
        return $this;
    }

    /**
     * Parses the style from the file.
     *
     * @param string $style Filename without .csl ending
     * @return \Geissler\CSL\Style\Style
     * @throws \ErrorException
     */
    public function readFile($style)
    {
        $file   =   $this->dir . '/' . $style . '.csl';
        if (file_exists($file) == true) {
            return $this->readXml(simplexml_load_file($file));
        }

        throw new \ErrorException('Style file (' . $file . ') not found!');
    }

    /**
     * Parses the style configuration from the SimpleXMLElement object.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Style\Style
     */
    public function readXml(\SimpleXMLElement $xml)
    {
        // store "global" configuration options in the context object
        Container::getContext()->addStyle('initializeWithHyphen', true);
        Container::getContext()->addStyle('demoteNonDroppingParticle', 'display-and-sort');

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'class':
                    Container::getContext()->addStyle('class', (string) $value);
                    break;
                case 'default-locale':
                    $locale =   Factory::locale();
                    $locale->readFile((string) $value);
                    Container::setLocale($locale);
                    break;
            }
        }

        // set global options and inheritable name options
        $options    =   new Options();
        $options->set('style', $xml);

        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'citation':
                    Container::setCitation(new Citation($child));
                    break;
                case 'bibliography':
                    Container::setBibliography(new Bibliography($child));
                    break;
                case 'macro':
                    $macroName   =   '';
                    foreach ($child->attributes() as $name => $value) {
                        if ($name == 'name') {
                            $macroName  =   (string) $value;
                            break;
                        }
                    }

                    if ($macroName !== '') {
                        Container::addMacro($macroName, new Macro($child));
                    }
                    break;
                case 'locale':
                    Container::getLocale()->addXml($child);
                    break;
            }
        }

        return $this;
    }
}
