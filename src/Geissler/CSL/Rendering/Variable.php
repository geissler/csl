<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Container;
use Geissler\CSL\Date\Format;

/**
 * Renders the text contents of a variable.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Variable implements Renderable, Groupable
{
    /** @var string * */
    private $name;
    /** @var string * */
    private $form;

    /**
     * Parses the variable configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->name = '';
        $this->form = '';

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'variable') {
                $this->name = (string) $value;
            } elseif ($name == 'form') {
                $this->form = (string) $value;
            }
        }
    }

    /**
     * Retrieve the variable name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Renders the variable.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        if ($this->form !== '') {
            $return = Container::getData()->getVariable($this->name . '-' . $this->form);
            if ($return !== null) {
                return $return;
            }

            if (is_object(Container::getAbbreviation()) == true) {
                $return = Container::getAbbreviation()->get($this->name, $this->form);
            }

            if ($return !== null) {
                return $return;
            }
        }

        // special cases
        switch ($this->name) {
            case 'title-short':
                $return = Container::getData()->getVariable('shortTitle');
                if ($return !== null) {
                    return $return;
                }
                break;
            case 'title':
                if ($this->form == 'short') {
                    $return = Container::getData()->getVariable('shortTitle');
                    if ($return !== null) {
                        return $return;
                    }
                }
                break;
            case 'citation-label':
                $return =   Container::getData()->getVariable($this->name);
                if ($return !== null) {
                    return $return;
                }

                // first 4 letters from the first two author family names and last to year digits
                $authors    =   Container::getData()->getVariable('author');
                $format     =   new Format();

                if (isset($authors[0]['family']) == true
                    && $format->format('issued') == true) {
                    $year   =   $format->getData();

                    if (isset($year[0]['year']) == true) {
                        switch (count($authors)) {
                            case 1:
                                $author =   substr($authors[0]['family'], 0, 4);
                                break;
                            case 2:
                                $author =   substr($authors[0]['family'], 0, 2) . substr($authors[1]['family'], 0, 2);
                                break;
                            default:
                                $author =   '';
                                for ($i = 0; $i < 4; $i++) {
                                    if (preg_match('/[A-Z]/', $authors[$i]['family'], $match) == 1) {
                                        $author .=  $match[0];
                                    }
                                }
                                break;
                        }

                        $length =   strlen($year[0]['year']);
                        return  $author . substr($year[0]['year'], $length - 2, $length);
                    }
                }
                break;
            case 'page':
                $format =   Container::getContext()->getValue('pageRangeFormat');
                if (is_object($format) == true) {
                    return $format->format(Container::getData()->getVariable($this->name));
                }
                break;
            case 'first-reference-note-number':
                // number of a preceding note containing the first reference to the item
                if (Container::getCitationItem()->get('noteIndex') !== null
                    || Container::getCitationItem()->get('index') !== null) {
                    return Container::getRendered()->getPositionOfFirstId(Container::getActualId());
                }
                break;
        }

        $return =   Container::getData()->getVariable($this->name);
        if ($return !== null) {
            return $return;
        }

        // retrieve variables form citations
        if (Container::getContext()->getName() == 'citation'
            && Container::getCitationItem() !== false) {

            $return =   Container::getCitationItem()->get($this->name);
            if ($return !== null) {
                return $return;
            }
        }

        return '';
    }

    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable()
    {
        if ($this->render('') === '') {
            return true;
        }

        return false;
    }
}
