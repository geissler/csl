<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Formating;

/**
 * Et-Al as children of a Names object.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class EtAl implements Renderable
{
    /** @var term **/
    private $term;
    /** @var Formating **/
    private $formating;

    /**
     * Parses the EtAl configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->term =   'et-al';

        $this->formating    =   new Formating($xml);

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
        return $this->formating->render($this->term);
    }
}
