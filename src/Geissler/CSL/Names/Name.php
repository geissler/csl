<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Contextualize;
use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formating;

/**
 * .
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Name implements Renderable, Contextualize
{
    /** @var string **/
    private $and;
    /** @var string **/
    private $delimiter;
    /** @var string **/
    private $delimiterPrecedesEtAl;
    /** @var string **/
    private $delimiterPrecedesLast;
    /** @var integer **/
    private $etAlMin;
    /** @var string **/
    private $etAlUseFirst;
    /** @var integer **/
    private $etAlSubsequentMin;
    /** @var string **/
    private $etAlSubsequentUseFirst;
    /** @var boolean **/
    private $etAlUseLast;
    /** @var string **/
    private $form;
    /** @var boolean **/
    private $initialize;
    /** @var string **/
    private $initializeWith;
    /** @var string **/
    private $nameAsSortOrder;
    /** @var string **/
    private $sortSeparator;
    /** @var Affix **/
    private $affix;
    /** @var Formating **/
    private $formating;

    /**
     * Parses the Name configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->and                      =   '';
        $this->delimiter                =   ',';
        $this->delimiterPrecedesEtAl    =   'contextual';
        $this->delimiterPrecedesLast    =   'contextual';
        $this->etAlMin                  =   0;
        $this->etAlUseFirst             =   '';
        $this->etAlSubsequentMin        =   0;
        $this->etAlSubsequentUseFirst   =   '';
        $this->etAlUseLast              =   false;

        $this->form             =   'long';
        $this->initialize       =   true;
        $this->initializeWith   =   '';
        $this->nameAsSortOrder  =   '';
        $this->sortSeparator    =   '';

        $this->affix        =   new Affix($xml);
        $this->formating    =   new Formating($xml);

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'and':
                    $this->and  =   (string) $value;
                    break;
                case 'delimiter':
                    $this->delimiter  =   (string) $value;
                    break;
                case 'delimiter-precedes-et-al':
                    $this->delimiterPrecedesEtAl  =   (string) $value;
                    break;
                case 'delimiter-precedes-last':
                    $this->delimiterPrecedesLast  =   (string) $value;
                    break;
                case 'et-al-min':
                    $this->etAlMin  =   (int) $value;
                    break;
                case 'et-al-use-first':
                    $this->etAlUseFirst  =   (string) $value;
                    break;
                case 'et-al-subsequent-min':
                    $this->etAlSubsequentMin  =   (int) $value;
                    break;
                case 'et-al-subsequent-use-first':
                    $this->etAlSubsequentUseFirst  =   (string) $value;
                    break;
                case 'et-al-use-last':
                    $this->etAlUseLast  =   (boolean) $value;
                    break;
                case 'form':
                    $this->form  =   (string) $value;
                    break;
                case 'initialize':
                    $this->initialize  =   (boolean) $value;
                    break;
                case 'initialize-with':
                    $this->initializeWith  =   (string) $value;
                    break;
                case 'name-as-sort-order':
                    $this->nameAsSortOrder  =   (string) $value;
                    break;
                case 'sort-separator':
                    $this->sortSeparator  =   (string) $value;
                    break;
            }
        }
    }

    /**
     * Applys the context configuration to the object.
     *
     * @return \Geissler\CSL\Interfaces\Contextualize
     */
    public function apply()
    {
        foreach (Container::getContext()->getOptions() as $name => $value) {
            if (property_exists($this, $name) == true) {
                $this->$name    =   $value;
            }
        }
        return $this;
    }

    /**
     * .
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {
        $this->apply();

        return '';
    }
}
