<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Modifiable;

/**
 * Display affixes.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Affix implements Renderable, Modifiable
{
    /** @var string **/
    private $prefix;
    /** @var string **/
    private $suffix;

    /**
     * Parses the affix configuration.
     *
     * @param \SimpleXMLElement $affix
     */
    public function __construct(\SimpleXMLElement $affix)
    {
        $this->prefix   =   '';
        $this->suffix   =   '';
        $this->modify($affix);
    }

    /**
     * Modifies the actual affix configuration.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Rendering\Affix
     */
    public function modify(\SimpleXMLElement $xml)
    {
        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'prefix') {
                $this->prefix   =   (string) $value;
            } elseif ($name == 'suffix') {
                $this->suffix   =   (string) $value;
            }
        }

        return $this;
    }

    /**
     * Retrieve the suffix if set.
     *
     * @return string
     */
    public function getSuffix()
    {
        if (isset($this->suffix) == true) {
            return $this->suffix;
        }

        return '';
    }

    /**
     * Adds the affixes.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        if ($data !== ''
            && $data !== null
            && ($this->prefix !== ''
                || $this->suffix !== '')) {
            $data   =   $this->addPrefix($this->addSuffix($data));

            // masc \n to get preg_replace working under Windows
            $data   =   str_replace("\n", '###', $data);
            // remove duplicated braces
            $regExp =   '/^' . preg_quote($this->prefix . $this->prefix) . '(.*)'
                . preg_quote($this->suffix . $this->suffix) . '$/';
            if (($this->prefix == '(')
                    && $this->suffix == ')'
                || ($this->prefix == '['
                    && $this->suffix == ']')) {
                $data   =   preg_replace($regExp, $this->prefix . '$1' . $this->suffix, $data);
            }

            $data   =   str_replace('###', "\n", $data);
        }

        return $data;
    }

    /**
     * Add a prefix inside a div or html element.
     *
     * @param  string$data
     * @return string
     */
    private function addPrefix($data)
    {
        if (preg_match('/^(<div[A-z| |=|"|\'|\-|0-9|\.]+>)(.*)/', $data, $match) == 1) {
            return $match[1] . $this->prefix . $match[2];
        }

        return $this->prefix . $data;
    }

    /**
     * Add a suffix inside a div or html element.
     *
     * @param  string$data
     * @return string
     */
    private function addSuffix($data)
    {
        if (preg_match('/(.*)(<\/div>)$/', $data, $match) == 1) {
            return $match[1] . $this->suffix . $match[2];
        }

        return $data . $this->suffix;
    }
}
