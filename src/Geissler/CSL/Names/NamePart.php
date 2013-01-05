<?php
namespace Geissler\CSL\Names;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Rendering\Formatting;
use Geissler\CSL\Rendering\TextCase;
use Geissler\CSL\Rendering\Affix;

/**
 * Name-Part.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class NamePart implements Renderable
{
    /** @var string **/
    private $name;
    /** @var Formatting **/
    private $formating;
    /** @var TextCase **/
    private $textCase;
    /** @var Affix **/
    private $affix;

    /**
     * Parses the NamePart configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->formating    =   new Formatting($xml);
        $this->textCase     =   new TextCase($xml);
        $this->affix        =   new Affix($xml);

        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'name') {
                $this->name =   (string) $value;
            }
        }
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Renders the name part.
     *
     * @param array $data
     * @return string
     */
    public function render($data)
    {
        $isInverted =   $this->isInverted($data);

        if ($this->name == 'given') {
            if (isset($data['given']) == true) {
                $data['given']  =   $this->formating->render($data['given']);
                $data['given']  =   $this->textCase->render($data['given']);
            }

            if (isset($data['dropping-particle']) == true) {
                $data['dropping-particle']  =   $this->formating->render($data['dropping-particle']);
                $data['dropping-particle']  =   $this->textCase->render($data['dropping-particle']);
            }

            // Affixes surround the "given" name-part, enclosing any demoted name particles for inverted names.
            $return =   array();
            $found  =   false;
            foreach ($data as $name => $value) {
                if ($name == 'given') {
                    $return[]   =   $value;
                    $found      =   true;
                } elseif ($found == true
                    && $isInverted == true
                    && ($name == 'non-dropping-particle'
                        || $name == 'dropping-particle')) {
                    $return[]   =   $value;
                }
            }

            return $this->affix->render(implode(' ', $return));
        } else {
            if (isset($data['family']) == true) {
                $data['family']  =   $this->formating->render($data['family']);
                $data['family']  =   $this->textCase->render($data['family']);
            }

            if (isset($data['non-dropping-particle']) == true) {
                $data['non-dropping-particle']  =   $this->formating->render($data['non-dropping-particle']);
                $data['non-dropping-particle']  =   $this->textCase->render($data['non-dropping-particle']);
            }

            // Affixes surround the "family" name-part, enclosing any preceding name particles, as well as
            // the "suffix" name-part for non-inverted names.
            $return =   array();
            $found  =   false;
            foreach ($data as $name => $value) {
                if ($found == false
                    && $isInverted == false
                    && ($name == 'non-dropping-particle'
                        || $name == 'dropping-particle')) {

                    $return[]   =   $value;
                } elseif ($found == true
                    && $isInverted == false
                    && $name == 'suffix') {

                    $return[]   =   $value;
                } elseif ($name == 'family') {
                    $return[]   =   $value;
                    $found      =   true;
                }
            }

            return $this->affix->render(implode(' ', $return));
        }
    }

    /**
     * Tests if the name order is inverted name, where the family name precedes the given name.
     *
     * @param array $data
     * @return boolean
     */
    private function isInverted($data)
    {
        $given  =   false;
        foreach (array_keys($data) as $name) {
            if ($name == 'family') {
                if ($given == false) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($name == 'given') {
                $given  =   true;
            }
        }
    }
}
