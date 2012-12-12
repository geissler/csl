<?php
namespace Geissler\CSL\Data;

/**
 * Abbreviations are not part of the CSL definition, but the citeproc-test implements this function.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Abbreviation
{
    /** @var array **/
    private $data;

    /**
     * Sets the abbreviations.
     *
     * @param string $json
     * @return \Geissler\CSL\Data\Abbreviation
     * @throws \ErrorException
     */
    public function set($json)
    {
        $data   =   json_decode($json, true);

        if (count($data) > 0) {
            $this->data =   $data;
            return $this;
        }

        throw new \ErrorException('No data set!');
    }

    /**
     * Retrieve a abbreviation.
     *
     * @param string $name
     * @param string $type short or long
     * @param string $group
     * @return string|null
     */
    public function get($name, $type = 'short', $group = 'default')
    {
        if (isset($this->data[$group]) == true
            && isset($this->data[$group][$name]) == true) {
            if ($type == 'short') {
                $data   = array_values($this->data[$group][$name]);
            } else {
                $data   = array_keys($this->data[$group][$name]);
            }

            return $data[0];
        }

        return null;
    }
}
