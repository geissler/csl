<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Container;

/**
 * Render a term.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Term implements Renderable
{
    /** @var string **/
    private $name;
    /** @var string **/
    private $form;
    /** @var string **/
    private $plural;
    /** @var array **/
    private $additional;

    /**
     * Parses the affix configuration.
     *
     * @param \SimpleXMLElement $term
     */
    public function __construct(\SimpleXMLElement $term)
    {
        $this->name         =   '';
        $this->form         =   '';
        $this->plural       =   'single';
        $this->additional   =   array();

        foreach ($term->attributes() as $name => $value) {
            switch ($name) {
                case 'term':
                    $this->name   =   (string) $value;
                    break;
                case 'form':
                    $this->form   =   (string) $value;
                    break;
                case 'plural':
                    if ((string) $value == 'true') {
                        $this->plural = 'multiple';
                    }
                    break;
                case 'gender':
                case 'match':
                    $this->additional[$name] =   (string) $value;
                    break;
            }
        }
    }

    /**
     * Adds the affixes.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        $return = Container::getLocale()->getTerms($this->name, $this->form, $this->plural, $this->additional);

        if ($return == null) {
            if ($this->name == 'year-suffix'
                && (Container::getContext()->in('sort') == true
                    || Container::getContext()->in('disambiguation') == true)) {
                return '';
            }

            switch ($this->form) {
                case 'verb-short':
                    $return = Container::getLocale()->getTerms($this->name, 'verb', $this->plural, $this->additional);
                    break;
                case 'symbol':
                    $return = Container::getLocale()->getTerms($this->name, 'short', $this->plural, $this->additional);
                    break;
                case 'verb':
                    $return = Container::getLocale()->getTerms($this->name, 'verb', $this->plural);
                    break;
                case 'symbol':
                    $return = Container::getLocale()->getTerms($this->name, 'symbol', $this->plural);
                    break;
            }

            if ($return == null) {
                switch ($this->form) {
                    case 'verb-short':
                    case 'symbol':
                    case 'verb':
                    case 'short':
                        $return = Container::getLocale()->getTerms($this->name, 'long');
                        break;
                }
            }

            if ($return == null) {
                $return =   Container::getLocale()->getTerms($this->name);
            }

            if ($return == null) {
                $return =   '';
            }
        }

        return $return;
    }
}
