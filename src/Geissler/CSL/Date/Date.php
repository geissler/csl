<?php
namespace Geissler\CSL\Date;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Display;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Date\DatePart;

/**
 * Renders dates.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Date implements Renderable
{
    /** @var Affix **/
    private $affix;
    /** @var Display **/
    private $display;
    /** @var Formating **/
    private $formating;
    /** @var TextCase **/
    private $textCase;
    /** @var string **/
    private $variable;
    /** @var string **/
    private $form;
    /** @var string **/
    private $delimiter;
    /** @var string **/
    private $dateParts;

    /**
     * Parses the affix configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $date)
    {
        $this->variable     =   '';
        $this->form         =   '';
        $this->dateParts    =   '';

        $this->affix        =   new Affix($date);
        $this->display      =   new Display($date);
        $this->formating    =   new Formating($date);
        $this->textCase     =   new TextCase($date);

        foreach ($date->attributes() as $name => $value) {
            switch ($name) {
                case 'variable':
                    $this->variable   =   (string) $value;
                    break;

                case 'form':
                    $this->form =   (string) $value;
                    break;

                case 'date-parts':
                    $this->dateParts    =   (string) $value;
                    break;

                case 'delimiter':
                    $this->delimiter    =   (string) $value;
                    break;
            }
        }

        foreach ($date->children() as $child) {
            if ($child->getName() == 'date-part') {
                if (is_array($this->dateParts) == false) {
                    $this->dateParts    =   array();
                }

                $this->dateParts[]  =   new DatePart($child);
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
