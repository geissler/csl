<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Affix;
use Geissler\CSL\Rendering\Display;
use Geissler\CSL\Rendering\Formating;
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
class Names implements Renderable
{
    /** @var array **/
    private $variables;
    /** @var string **/
    private $delimiter;
    /** @var Affix **/
    private $affix;
    /** @var Display **/
    private $display;
    /** @var Formating **/
    private $formating;
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
     * @param \SimpleXMLElement $date
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->variables    =   array();
        $this->delimiter    =   '';
        $this->name         =   new Name(new \SimpleXMLElement('<name />'));

        $this->affix        =   new Affix($xml);
        $this->display      =   new Display($xml);
        $this->formating    =   new Formating($xml);

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
     * .
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
                $this->label->setVariable('editortranslator');
                $editorTrans    .=   $this->label->render('');
            }

            $returns    =   array($editorTrans);
        }

        $return =   implode($this->delimiter, $returns);
        $return =   $this->formating->render($return);
        $return =   $this->display->render($return);
        return $this->affix->render($return);
    }
}
