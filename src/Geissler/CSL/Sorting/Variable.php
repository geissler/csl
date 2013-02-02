<?php
namespace Geissler\CSL\Sorting;

use Geissler\CSL\Interfaces\Sortable;
use Geissler\CSL\Container;
use Geissler\CSL\Names\Name;
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
                $name   =   new Name(new \SimpleXMLElement('<name form="long" name-as-sort-order="all"/>'));
                return $name->render(Container::getData()->getVariable($this->variable));
                break;
            case 'accessed':
            case 'container':
            case 'event-date':
            case 'issued':
            case 'original-date':
            case 'submitted':
                // 00000000 represent the first date and if existing a second date of a date range
                $format =   new Format();
                if ($format->format($this->variable) == true) {
                    $data   =   $format->getData();

                    $first  =   $this->formatDate($data[0]);
                    $second =   '00000000';
                    if (isset($data[1]) == true) {
                        $second =   $this->formatDate($data[1]);

                        if ($second < $first) {
                            return $second . $first;
                        }

                        return $first . $second;
                    }

                    return $first . $second;
                }

                // put empty dates in bibliographies at the end
                if (Container::getContext()->getName() == 'bibliography') {
                    return '';
                }

                return '0000000000000000';
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
                return preg_replace(
                    '/(<.*?>)(.*?)(<\/.*?>)/',
                    '$2',
                    Container::getData()->getVariable($this->variable)
                );
                break;
        }
    }

    /**
     * Formats a date to be sorted.
     *
     * @param array $data
     * @return string
     */
    private function formatDate($data)
    {
        if ($data['year'] == '') {
            $data['year']   =   '0000';
        }

        if ($data['month'] == '') {
            $data['month']  =   '00';
        } elseif ($data['month'] < 10) {
            $data['month'] = '0' . $data['month'];
        }

        if ($data['day'] == '') {
            $data['day'] = '00';
        } elseif ($data['day'] < 10) {
            $data['day'] = '0' . $data['day'];
        }

        return $data['year'] . $data['month'] . $data['day'];
    }
}
