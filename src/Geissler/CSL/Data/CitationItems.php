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
    public function getAtPosition($variable, $position, $groupPosition = false)
    {
        if ($groupPosition == false) {
            $groupPosition  =   $this->groupPosition;
        }

        if (isset($this->data[$position]) == true
            && isset($this->data[$position][$groupPosition]) == true
            && isset($this->data[$position][$groupPosition][$variable]) == true) {
            return $this->data[$position][$groupPosition][$variable];
        }

        return null;
    }

    public function sortGroup(array $group)
    {
        $newOrder   =   array();

        foreach (array_keys($group) as $id) {
            $newOrder[] =   array('id' => $id);
        }

        $this->data[$this->position]    =   $newOrder;
        $this->groupPosition            =   0;
        return $this;
    }

    protected function getGroupLength()
    {
        return count($this->data[$this->position]);
    }
}
