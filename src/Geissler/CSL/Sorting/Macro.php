<?php
namespace Geissler\CSL\Sorting;

use Geissler\CSL\Interfaces\Sortable;
use Geissler\CSL\Macro\Call;
use Geissler\CSL\Container;

/**
 * Sorting keys with macros.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Macro implements Sortable
{
    /** @var Call **/
    private $macro;
    /** @var array */
    private $nameModifications;

    /**
     * Parses the macro key configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->nameModifications    =   array();

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'macro':
                    $this->macro    =   new Call($value);
                    break;
                case 'names-min':
                    $this->nameModifications[]  =   'et-al-min="' . (int) $value . '"';
                    break;
                case 'names-use-first':
                    $this->nameModifications[]  =   'et-al-use-first="' . (int) $value . '"';
                    break;
                case 'names-use-last':
                    $this->nameModifications[]  =   'et-al-use-last="' . (int) $value . '"';
                    break;
            }
        }
    }

    /**
     * Render the data with the macro.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        $macro  =   clone $this->macro->getMacro();
        $macro->modifyChildElement(
            'Geissler\CSL\Names\Names',
            new \SimpleXMLElement('<macro ' . implode(' ', $this->nameModifications) . ' />')
        );

        return $this->removeEtAl($macro->render($data));
    }

    /**
     * When et-al abbreviation occurs, the "et-al" and "and others" terms are excluded from the sort key values.
     *
     * @param string $value
     * @return string
     */
    private function removeEtAl($value)
    {
        $value  =   str_replace(Container::getLocale()->getTerms('et-al'), '', $value);
        return str_replace(Container::getLocale()->getTerms('and others'), '', $value);
    }
}
