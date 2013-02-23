<?php
namespace Geissler\CSL\Data;

use Geissler\CSL\Data\CitationAbstract;

/**
 * Data container for the simple citation-items.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class CitationItems extends CitationAbstract
{
    /**
     * Retrieve a variable at a position.
     *
     * @param string $variable
     * @param int $position
     * @param bool $groupPosition
     * @return int|null|string
     */
    public function getAtPosition($variable, $position, $groupPosition = false)
    {
        if ($groupPosition === false) {
            $groupPosition  =   $this->groupPosition;
        }

        if (isset($this->data[$position]) == true
            && isset($this->data[$position][$groupPosition]) == true
            && isset($this->data[$position][$groupPosition][$variable]) == true) {
            return $this->data[$position][$groupPosition][$variable];
        }

        return null;
    }

    /**
     * Set a variable.
     *
     * @param string $name
     * @param mixed $value
     * @param integer $position
     * @return bool
     */
    public function setVariable($name, $value, $position)
    {
        if (isset($this->data[$position]) == true) {
            $length =   count($this->data[$position]);
            for ($i = 0; $i < $length; $i++) {
                $this->data[$position][$i][$name] =   $value;
            }

            return true;
        }

        return false;
    }


    /**
     * Order the values in the actual group by the given order.
     *
     * @param array $group
     * @param bool $byKeys
     * @return CitationItems
     */
    public function sortGroup(array $group, $byKeys = true)
    {
        $newOrder   =   array();
        if ($byKeys == true) {
            $order  =   array_keys($group);
        } else {
            $order  =   $group;
        }

        foreach ($order as $id) {
            $newOrder[] =   array('id' => $id);
        }

        foreach ($order as $id) {
            $this->moveToFirstInGroup();
            do {
                if ($id == $this->get('id')) {
                    $newOrder[]   = $this->data[$this->position][$this->groupPosition];
                    break;
                }
            } while ($this->nextInGroup() == true);
        }

        $this->data[$this->position]    =   $newOrder;
        $this->groupPosition            =   0;
        return $this;
    }

    /**
     * Retrieve the actual group length.
     * @return int
     */
    protected function getGroupLength()
    {
        return count($this->data[$this->position]);
    }
}
