<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Container;

/**
 * Et-Al as children of a Names object.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class EtAl implements Renderable
{
    /** @var string **/
    private $term;
    /** @var Formatting **/
    private $formatting;

    /**
     * Parses the EtAl configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->term         =   'et-al';
        $this->formatting   =   new Formatting($xml);

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'term') {
                $this->term =   (string) $value;
            }
        }
    }

    /**
     * Et-La rendering.
     *
     * @param string|array $data
     * @return string
     */
    public function render($data)
    {
        $data   =   str_replace(' ' . Container::getLocale()->getTerms('et-al'), '', $data);
        $data   .=  ' ' . $this->formatting->render(Container::getLocale()->getTerms($this->term));

        return $data;
    }
}
