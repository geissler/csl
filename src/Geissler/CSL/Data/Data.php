<?php
namespace Geissler\CSL\Data;

/**
 * Storage for the data to parse.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Data
{
    /** @var array **/
    private $data;
    /** @var integer **/
    private $length;
    /** @var integer **/
    private $position;

    /**
     * Set the data for citation and bibliography.
     *
     * @param string $json JSON array
     * @return boolean
     * @throws \ErrorException
     */
    public function set($json)
    {
        $data           =   json_decode($json, true);
        $this->length   =   count($data);

        if ($this->length > 0) {
            $this->data     =   $data;
            $this->position =   0;

            return true;
        }

        throw new \ErrorException('No data set!');
    }

    /**
     * Retrieve actual entry.
     *
     * @return array|null
     */
    public function get()
    {
        if (isset($this->data[$this->position]) == true) {
            return $this->data[$this->position];
        }

        return null;
    }

    /**
     * Retrieve a variable from the actual entry.
     *
     * @param string $name
     * @return string|null
     */
    public function getVariable($name)
    {
        if (isset($this->data[$this->position]) == true
            & array_key_exists($name, $this->data[$this->position]) == true) {
                return $this->data[$this->position][$name];
        }

        return null;
    }

    /**
     * Move position to next entry.
     *
     * @return boolean
     */
    public function next()
    {
        $this->position++;

        if ($this->position < $this->length) {
            return true;
        }

        return false;
    }

    /**
     * Changes the data position to the item with the given id.
     *
     * @param integer|string $id
     * @return boolean
     */
    public function moveToId($id)
    {
        for ($i = 0; $i < $this->length; $i++) {
            if (isset($this->data[$i]['id']) == true
                && $this->data[$i]['id'] == $id) {
                $this->position =   $i;
                return true;
            }
        }

        return false;
    }
}
