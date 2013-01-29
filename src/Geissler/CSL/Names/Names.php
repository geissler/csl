<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Interfaces\Modifiable;
use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Display;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Names\Name;
use Geissler\CSL\Names\EtAl;
use Geissler\CSL\Names\Substitute;
use Geissler\CSL\Rendering\Label;

/**
 * Names.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Names implements Groupable, Modifiable
{
    /** @var array **/
    private $variables;
    /** @var string **/
    private $delimiter;
    /** @var Affix **/
    private $affix;
    /** @var Display **/
    private $display;
    /** @var Formatting **/
    private $formatting;
    /** @var Name **/
    private $name;
    /** @var EtAl **/
    private $etAl;
    /** @var Substitute **/
    private $substitute;
    /** @var Label **/
    private $label;

    /**
     * Parses the Names configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->variables    =   array();
        $this->delimiter    =   '';
        $this->name         =   new Name(new \SimpleXMLElement('<name form="long" />'));

        $this->affix        =   new Affix($xml);
        $this->display      =   new Display($xml);
        $this->formatting   =   new Formatting($xml);

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'variable':
                    $this->variables    =   explode(' ', (string) $value);
                    break;
                case 'delimiter':
                    $this->delimiter    =   (string) $value;
                    break;
            }
        }

        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'name':
                    $this->name         =   new Name($child);
                    break;
                case 'et-al':
                    $this->etAl         =   new EtAl($child);
                    break;
                case 'substitute':
                    $this->substitute   =   new Substitute($child);
                    break;
                case 'label':
                    $this->label        =   new Label($child);
                    break;
            }
        }

        // modify substitute child by passing all options
        if (isset($this->name) == true
            && isset($this->substitute) == true) {
            $options    =   $this->name->getOptions();

            if ($this->delimiter !== '') {
                $options['delimiter']   =   $this->delimiter;
            }

            $xmlOptions =   array();
            foreach ($options as $field => $value) {
                $xmlOptions[]   =   $field . '="' . $value . '"';
            }

            $this->substitute->modify(new \SimpleXMLElement('<names ' . implode(' ', $xmlOptions) . '/>'));
        }
    }

    /**
     * New configurations are passed to the name and substitute object.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Names\Names
     */
    public function modify(\SimpleXMLElement $xml)
    {
        $this->name->modify($xml);

        if (isset($this->substitute) == true) {
            $this->substitute->modify($xml);
        }

        return $this;
    }

    /**
     * Retrieve the name object.
     *
     * @return \Geissler\CSL\Names\Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieve the variables to call.
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Retrieve the delimiter for the names set in names or name.
     *
     * @return string
     */
    public function getDelimiter()
    {
        if ($this->delimiter == '') {
            $options    =   $this->name->getOptions();
            return $options['delimiter'];
        }

        return $this->delimiter;
    }

    /**
     * Render the names.
     *
     * @param string|array $data
     * @return string
     */
    public function render($data)
    {
        $returns        =   array();
        $compare        =   array();
        $lastSubstitute =   Container::getContext()->getSubstitute()->getVariable();

        foreach ($this->variables as $variable) {
            // don't render if variable is already used as substitute value
            if ($lastSubstitute === $variable) {
                return '';
            }

            $names      =   Container::getData()->getVariable($variable);
            $content    =   $this->name->render($names);

            // et-al
            if (isset($this->etAl) == true
                && Container::getContext()->getValue('etAlMin', Container::getContext()->getName()) !== null
                && Container::getContext()->getValue('etAlMin', Container::getContext()->getName()) <= count($names)) {
                $content    .=   $this->etAl->render($content);
            }

            $compare[$variable] =   $content;

            // use substitute
            if ($content == ''
                && isset($this->substitute) == true) {
                $content    =   $this->substitute->render('');

                if (Container::getContext()->getSubstitute()->getVariable() !== '') {
                    $variable   =   Container::getContext()->getSubstitute()->getVariable();
                }
            }

            if ($content != ''
                && isset($this->label) == true
                && Container::getContext()->in('sort') == false) {
                $this->label->setVariable($variable);
                $content    .=   $this->label->render($content);
            }

            $returns[]  =   $content;
        }

        // The one exception: when the selection consists of "editor" and "translator", and when the contents
        // of these two name variables is identical, then the contents of only one name variable is rendered.
        if (in_array('editor', $this->variables) == true
            && in_array('translator', $this->variables) == true
            && $compare['editor'] == $compare['translator']) {

            $editorTrans    =   $compare['translator'];
            if (isset($this->label) == true) {
                $plural =   'singular';
                if (count($this->variables) > 1) {
                    $plural = 'multiple';
                }

                $this->label
                    ->setVariable('editortranslator')
                    ->setPlural($plural);

                $editorTrans    .=   $this->label->render('');
            }

            $returns    =   array($editorTrans);
        }

        $return =   implode($this->delimiter, $returns);
        $return =   $this->formatting->render($return);
        $return =   $this->display->render($return);
        return $this->affix->render($return);
    }

    /**
     * Render only the names and return them as an array.
     *
     * @param array $data
     * @return array
     */
    public function renderAsArray($data)
    {
        $returns    =   array();

        foreach ($this->variables as $variable) {
            $data       =   Container::getData()->getVariable($variable);
            $options    =   Container::getContext()->getDisambiguationOptions('Geissler\CSL\Names\Name');
            $length     =   count($data);

            if (isset($options['etAlUseFirst']) == true) {
                if ($options['etAlUseFirst'] < $length) {
                    $length =   $options['etAlUseFirst'];
                }
            } elseif (Container::getContext()->getValue('etAlUseFirst', 'citation') !== ''
                && Container::getContext()->getValue('etAlUseFirst', 'citation') < $length) {
                $length =   Container::getContext()->getValue('etAlUseFirst', 'citation');
            }

            for ($i = 0; $i < $length; $i++) {
                $returns[]  =   $this->name->render(array($data[$i]));
            }
        }

        return $returns;
    }

    /**
     * Retrieve the maximal number of renderable names.
     *
     * @return int
     */
    public function getMaxNumberOfNames()
    {
        $max    =   0;
        foreach ($this->variables as $variable) {
            if (count(Container::getData()->getVariable($variable)) > $max) {
                $max    =   count(Container::getData()->getVariable($variable));
            }
        }

        return $max;
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        foreach ($this->variables as $variable) {
            if (Container::getData()->getVariable($variable) !== null) {
                return false;
            }
        }

        if (isset($this->substitute) == true) {
            return $this->substitute->hasAccessEmptyVariable();
        }

        return true;
    }
}
