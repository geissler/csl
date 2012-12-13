<?php
namespace Geissler\CSL\Data;

/**
 * Data container for the citation items.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Citation
{
    /** @var array **/
    private $data;
    /** @var integer **/
    private $length;
    /** @var integer **/
    private $position;
    /** @var integer **/
    private $groupPosition;
    /** @var integer **/
    private $groupLength;

    /**
     * Sets the citation items.
     *
     * @param string $json
     * @return \Geissler\CSL\Data\Citation
     * @throws \ErrorException
     */
    public function set($json)
    {
        $data           =   json_decode($json, true);
        $this->length   =   count($data);

        if ($this->length > 0) {
            $this->data             =   $data;
            $this->position         =   0;
            $this->groupPosition    =   0;
            $this->groupLength      =   count($this->data[$this->position]);

            return $this;
        }

        throw new \ErrorException('No citation data set!');
    }

    /**
     * Retrieve the variable from the actual citation item.
     * @param string $variable
     * @return string|null
     */
    public function get($variable)
    {
        if (isset($this->data[$this->position]) == true
            && isset($this->data[$this->position][$this->groupPosition]) == true
            && isset($this->data[$this->position][$this->groupPosition][$variable]) == true) {
                return $this->data[$this->position][$this->groupPosition][$variable];
        }

        return null;
    }

    /**
     * Move to the next citation item in the actual citation group.
     *
     * @return boolean
     */
    public function nextInGroup()
    {
        $this->groupPosition++;

        if ($this->groupPosition < $this->groupLength) {
            return true;
        }

        return false;
    }

    /**
     * Move to the next citation group.
     *
     * @return boolean
     */
    public function next()
    {
        $this->position++;

        if ($this->position < $this->length) {
            $this->groupPosition    =   0;
            $this->groupLength      =   count($this->data[$this->position]);
            return true;
        }

        return false;
    }
}
