<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\RenderableElement;
use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Formating;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Rendering\StripPeriods;

/**
 * Label element.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Label implements RenderableElement
{
    /** @var string **/
    private $variable;
    /** @var string **/
    private $form;
    /** @var string **/
    private $plural;
    /** @var Affix **/
    private $affix;
    /** @var Formating **/
    private $formating;
    /** @var TextCase **/
    private $textCase;
    /** @var StripPeriods **/
    private $stripPeriods;

    /**
     * Parses the Label configuration.
     *
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->form     =   'long';
        $this->plural   =   'contextual';

        $this->affix        =   new Affix($xml);
        $this->formating    =   new Formating($xml);
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
                    $this->plural   =   (string) $value;
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

        $content    =   Container::getData()->getVariable($this->variable);
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
                $plural =   'multiple';
                break;
        }

        $form   =   '';
        if ($this->form !== 'long') {
            $form   =   $this->form;
        }

        $return =   Container::getLocale()->getTerms($this->variable, $form, $plural);
        $return =   $this->formating->render($return);
        $return =   $this->textCase->render($return);
        $return =   $this->stripPeriods->render($return);
        return $this->affix->render($return);
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
