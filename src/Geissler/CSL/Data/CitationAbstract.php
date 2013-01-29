<?php
namespace Geissler\CSL\Data;

use Geissler\CSL\Container;

/**
 * Data container for citation data.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
abstract class CitationAbstract
{
    /** @var array **/
    protected $data;
    /** @var integer **/
    protected $length;
    /** @var integer **/
    protected $position;
    /** @var integer **/
    protected $groupPosition;
    /** @var integer **/
    protected $groupLength;

    /**
     * Sets the citation items.
     *
     * @param string $json
     * @return \Geissler\CSL\Data\CitationItems
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
            $this->groupLength      =   $this->getGroupLength();

            return $this;
        }

        throw new \ErrorException('No citation data set! Correct json object?');
    }

    /**
     * Retrieve the variable from the actual citation item.
     *
     * @param string $variable
     * @return string|integer|null
     */
    public function get($variable)
    {
        return $this->getAtPosition($variable, $this->position);
    }

    /**
     * Retrieve the variable from a given position.
     *
     * @param string $variable
     * @param integer $position
     * @param bool|integer $groupPosition
     * @return string|integer|null
     */
    abstract public function getAtPosition($variable, $position, $groupPosition = false);

    /**
     * Move to the next citation item in the actual citation group.
     *
     * @return boolean
     */
    public function nextInGroup()
    {
        Container::getContext()->getSubstitute()->clear();
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
        Container::getContext()->getSubstitute()->clear();
        $this->position++;

        if ($this->position < $this->length) {
            $this->groupPosition    =   0;
            $this->groupLength      =   $this->getGroupLength();
            return true;
        }

        return false;
    }

    /**
     * Move to first citation entry.
     *
     * @return CitationAbstract
     */
    public function moveToFirst()
    {
        Container::getContext()->getSubstitute()->clear();
        $this->position         =   0;
        $this->groupPosition    =   0;
        $this->groupLength      =   $this->getGroupLength();
        return $this;
    }

    /**
     * Move to first entry in citation group.
     *
     * @return CitationAbstract
     */
    public function moveToFirstInGroup()
    {
        Container::getContext()->getSubstitute()->clear();
        $this->groupPosition    =   0;
        return $this;
    }

    /**
     * Access the actual position.
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Access the actual group position.
     * @return int
     */
    public function getGroupPosition()
    {
        return $this->groupPosition;
    }

    /**
     * Access total number of citations.
     *
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Sort citation items if there is only one entry per cite or sort all groups by new order.
     *
     * @param array $order
     * @return bool
     */
    public function sort(array $order)
    {
        $this->moveToFirst();

        if ($this->getGroupLength() == 1) {
            $newOrder   =   array();
            foreach ($order as $id) {
                $this->moveToFirst();
                do {
                    if ($id == $this->get('id')) {
                        $newOrder[]   = $this->data[$this->position];
                        break;
                    }
                } while ($this->next() == true);
            }

            if (count($this->data) == count($newOrder)) {
                $this->data =   $newOrder;
                $this->moveToFirst();
                return true;
            }

            return false;
        } else {
            // sort groups
            do {
                $this->sortGroup($order, false);
            } while ($this->next() == true);
        }

        return false;
    }

    /**
     * Stores the new order of the citation-items.
     *
     * @param array $group new order
     * @param bool $byKeys sort by the values of the keys or the actual values of the group-array
     * @return CitationAbstract
     */
    abstract public function sortGroup(array $group, $byKeys = true);

    /**
     * Calculate the length of the actual citations items.
     * @return integer
     */
    abstract protected function getGroupLength();
}
