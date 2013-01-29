<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Rendering\StripPeriods;

/**
 * Label element.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Label implements Groupable
{
    /** @var string **/
    private $variable;
    /** @var string **/
    private $form;
    /** @var string **/
    private $plural;
    /** @var Affix **/
    private $affix;
    /** @var Formatting **/
    private $formating;
    /** @var TextCase **/
    private $textCase;
    /** @var StripPeriods **/
    private $stripPeriods;

    /**
     * Parses the Label configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->form     =   'long';
        $this->plural   =   'contextual';

        $this->affix        =   new Affix($xml);
        $this->formating    =   new Formatting($xml);
        $this->textCase     =   new TextCase($xml);
        $this->stripPeriods =   new StripPeriods($xml);

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'variable':
                    $this->setVariable((string) $value);
                    break;
                case 'form':
                    $this->form     =   (string) $value;
                    break;
                case 'plural':
                    $this->setPlural((string) $value);
                    break;
            }
        }
    }

    /**
     * Change the variable value.
     *
     * @param string $variable
     * @return \Geissler\CSL\Rendering\Label
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;
        return $this;
    }

    /**
     * Change the plural value.
     *
     * @param string $plural
     * @return \Geissler\CSL\Rendering\Label
     */
    public function setPlural($plural)
    {
        $this->plural = $plural;
        return $this;
    }

    /**
     * Renders the label.
     *
     * @param string|array $data
     * @return string
     * @throws \ErrorException If the variable parameter is not set
     */
    public function render($data)
    {
        if (isset($this->variable) == false) {
            throw new \ErrorException('variable is not set!');
        }

        $variable   =   $this->variable;
        if ($this->variable == 'locator') {
            // Must be accompanied in the input data by a label indicating the locator type, which determines which
            // term is rendered by cs:label when the "locator" variable is selected
            if (is_object(Container::getCitationItem()) == true
                && Container::getCitationItem()->get('label') !== null) {
                $variable   =   Container::getCitationItem()->get('label');
            } else {
                return '';
            }
        }

        // The term is only rendered if the selected variable is non-empty.
        $content    =   Container::getData()->getVariable($variable);
        if ($content == '') {
            return '';
        }

        $plural     =   'single';
        switch ($this->plural) {
            case 'contextual':
                if (is_array($content) == true) {
                    if (count($content) > 1) {
                        $plural =   'multiple';
                    }
                } elseif (($this->variable == 'number-of-pages'
                    || $this->variable == 'number-of-volumes')
                    && preg_match_all('/([0-9])/', $content) > 1) {

                    $plural =   'multiple';
                } elseif (preg_match('/([2-9][0-9]*)/', $content) == 1) {
                    $plural =   'multiple';
                }
                break;
            case 'always':
            case 'multiple':
                $plural =   'multiple';
                break;
        }

        $form   =   '';
        if ($this->form !== 'long') {
            $form   =   $this->form;
        }

        $return =   Container::getLocale()->getTerms($variable, $form, $plural);
        if ($return !== '') {
            $return =   $this->formating->render($return);
            $return =   $this->textCase->render($return);
            $return =   $this->stripPeriods->render($return);
            $return =   $this->affix->render($return);
        }

        return $return;
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        if (Container::getData()->getVariable($this->variable) === null) {
            return true;
        }

        return false;
    }
}
