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
     * @param string $type
     * @param string $value
     * @param string $group
     * @return string|null
     */
    public function get($type, $value, $group = 'default')
    {
        if (isset($this->data[$group]) == true
            && isset($this->data[$group][$type]) == true) {

            if (isset($this->data[$group][$type][$value]) == true) {
                return $this->data[$group][$type][$value];
            }

            $key    =   array_search($value, $this->data[$group][$type]);
            if ($key !== false) {
                return $key;
            }
        }

        return $this->getChild($type, $value, $group);
    }

    /**
     * Test if type if child element of group.
     *
     * @param string $type
     * @param string $value
     * @param string $group
     * @return null|string
     */
    private function getChild($type, $value, $group)
    {
        $children   =   array(
            'chapter-number'    =>  'number',
            'collection-number' =>  'number',
            'edition'           =>  'number',
            'issue'             =>  'number',
            'number-of-pages'   =>  'number',
            'number-of-volumes' =>  'number',
            'volume'            =>  'number'
        );

        if (array_key_exists($type, $children) == true) {
            return $this->get($children[$type], $value, $group);
        }

        return null;
    }
}
