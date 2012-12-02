<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\StripPeriods;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Rendering\Affix;

/**
 * Renders a part of a date.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class DateParts implements Renderable
{
    /** @var string **/
    private $name;
    /** @var string **/
    private $form;
    /** @var StripPeriods **/
    private $stripPeriods;
    /** @var Affix **/
    private $affix;
    /** @var Formating **/
    private $formating;
    /** @var TextCase **/
    private $textCase;

    /**
     * Parses the affix configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $date)
    {
        $this->name =   '';
        $this->form =   '';

        $this->stripPeriods =   new StripPeriods($date);
        $this->formating    =   new Formating($date);
        $this->textCase     =   new TextCase($date);
        $this->affix        =   new Affix($date);

        foreach ($date->attributes() as $name => $value) {
            switch ($name) {
                case 'name':
                    $this->name   =   (string) $value;
                    break;

                case 'form':
                    $this->form =   (string) $value;
                    break;
            }
        }
    }

    /**
     * Adds the display options.
     *
     * @param string $data
     * @return string
     * @todo Full support of left-margin and right-inline
     * @link http://citationstyles.org/downloads/specification.html#display display
     */
    public function render($data)
    {

    }
}
