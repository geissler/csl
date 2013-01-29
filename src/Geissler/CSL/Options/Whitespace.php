<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Interfaces\Optional;

/**
 * Whitespace.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Whitespace implements Optional
{
    /** @var bool */
    private $hangingIndent;
    /** @var string */
    private $secondFieldAlign;
    /** @var int */
    private $lineSpacing;
    /** @var int */
    private $entrySpacing;

    /**
     * Configure the whitespace options.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->hangingIndent    =   false;
        $this->lineSpacing      =   1;
        $this->entrySpacing     =   1;
        $this->secondFieldAlign =   '';

        foreach ($xml->attributes() as $name => $value) {
            switch ($name) {
                case 'hanging-indent':
                    $this->hangingIndent    =   isBoolean($value);
                    break;
                case 'second-field-align':
                    $this->secondFieldAlign =   (string) $value;
                    break;
                case 'line-spacing':
                    $this->lineSpacing      =   (integer) $value;
                    break;
                case 'entry-spacing':
                    $this->entrySpacing     =   (integer) $value;
                    break;
            }
        }
    }

    /**
     * Apply the optional modification.
     *
     * @param array $data
     * @return array|string
     */
    public function apply(array $data)
    {
        if (is_array($data[0]) == true) {




        } else {
            if ($this->secondFieldAlign !== '') {
                $data   =   $this->secondFieldAlign($data);
            }

        }


        return $data;
    }

    /**
     * If set, subsequent lines of bibliographic entries are aligned along the second field.
     *
     * @param string $data
     * @return string
     */
    private function secondFieldAlign($data)
    {
        if (is_array($data) == true
            && strpos($data[0], '<div class="csl-left-margin">') === false) {
            return '<div class="csl-left-margin">' . $data[0] . '</div>'
                . '<div class="csl-right-inline">' . implode('', array_splice($data, 1)) . '</div>';
        }

        return $data;
    }
}
