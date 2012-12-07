<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Display;
use Geissler\CSL\Rendering\Formating;

/**
 * .
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Names implements Renderable
{
    /** @var array **/
    private $variables;
    /** @var children **/
    private $children;
    /** @var Affix **/
    private $affix;
    /** @var Display **/
    private $display;
    /** @var Formating **/
    private $formating;

    /**
     * Parses the Names configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->variables    =   array();
        $this->children     =   array();

        $this->affix        =   new Affix($xml);
        $this->display      =   new Display($xml);
        $this->formating    =   new Formating($xml);

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'variable') {
                $this->variables    = explode(' ', (string) $value);
            }
        }
    }

    /**
     * .
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {

    }
}
