<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Groupable;
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
class Names implements Groupable
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
        $this->name         =   new Name(new \SimpleXMLElement('<name form="short" />'));

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
    }

    /**
     * @return \Geissler\CSL\Names\Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Render the names.
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {
        $returns    =   array();
        $compare    =   array();

        foreach ($this->variables as $variable) {
            $content    =   $this->name->render(Container::getData()->getVariable($variable));

            if (isset($this->etAl) == true) {
                $content    =   $this->etAl->render($content);
            }

            $compare[$variable] =   $content;

            if ($content != ''
                && isset($this->label) == true) {
                $this->label->setVariable($variable);
                $content    .=   $this->label->render($content);
            }

            if ($content == ''
                && isset($this->substitute) == true) {

                $content    =   $this->substitute->render('');
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

    public function renderAsArray($data)
    {
        $returns    =   array();

        foreach ($this->variables as $variable) {
            $data   =   Container::getData()->getVariable($variable);
            $length =   count($data);

            for ($i = 0; $i < $length; $i++) {
                $returns[]  =   $this->name->render(array($data[$i]));
            }
        }

        return $returns;
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
