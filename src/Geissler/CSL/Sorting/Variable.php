<?php
namespace Geissler\CSL\Sorting;

use Geissler\CSL\Interfaces\Sortable;
use Geissler\CSL\Container;
use Geissler\CSL\Date\Format;

/**
 * Sorting keys with variables.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Variable implements Sortable
{
    /** @var string */
    private $variable;
    /** @var string */
    private $sort;

    /**
     * Parse the variable name.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'variable':
                    $this->variable =   (string) $value;
                    break;
                case 'sort':
                    $this->sort =   (string) $value;
                    break;
            }
        }

    }
    /**
     * Render the element.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        switch ($this->variable) {
            case 'author':
            case 'collection-editor':
            case 'composer':
            case 'container-author':
            case 'director':
            case 'editor':
            case 'editorial-director':
            case 'illustrator':
            case 'interviewer':
            case 'original-author':
            case 'recipient':
            case 'reviewed-author':
            case 'translator':
                return Container::getData()->getVariable($this->variable . '-long');
                break;
            case 'accessed':
            case 'container':
            case 'event-date':
            case 'issued':
            case 'original-date':
            case 'submitted':
                $format =   new Format();
                if ($format->format($this->variable) == true) {
                    $data   =   $format->getData();

                    if ($data[0]['year'] == '') {
                        $data[0]['year']   =   '0000';
                    }

                    if ($data[0]['month'] == '') {
                        $data[0]['month']  =   '00';
                    } elseif ($data[0]['month'] < 10) {
                        $data[0]['month'] = '0' . $data[0]['month'];
                    }

                    if ($data[0]['day'] == '') {
                        $data[0]['day'] = '00';
                    } elseif ($data[0]['day'] < 10) {
                        $data[0]['day'] = '0' . $data[0]['day'];
                    }

                    return $data[0]['year'] . $data[0]['month'] . $data[0]['day'];
                }

                return '00000000';
                break;
            case 'chapter-number':
            case 'collection-number':
            case 'edition':
            case 'issue':
            case 'number':
            case 'number-of-pages':
            case 'number-of-volumes':
            case 'volume':
                return (int) Container::getData()->getVariable($this->variable);
                break;
            default:
                return Container::getData()->getVariable($this->variable);
                break;
        }
    }
}
