<?php
namespace Geissler\CSL\Data;

use Geissler\CSL\Data\CitationAbstract;

/**
 * Data container for the more complex citation data.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Citations extends CitationAbstract
{
    /**
     * Retrieve the variable from a given position.
     *
     * @param string $variable
     * @param integer $position
     * @param bool|integer $groupPosition
     * @return string|integer|null
     */
    public function getAtPosition($variable, $position, $groupPosition = false)
    {
        if ($groupPosition === false) {
            $groupPosition  =   $this->groupPosition;
        }

        if (isset($this->data[$position]) == true) {
            switch ($variable) {
                case 'id':
                case 'locator':
                case 'label':
                case 'prefix':
                case 'suffix':
                    if (isset($this->data[$position][0]['citationItems']) == true
                        && isset($this->data[$position][0]['citationItems'][$groupPosition]) == true
                        && isset($this->data[$position][0]['citationItems'][$groupPosition][$variable]) == true) {
                        return $this->data[$position][0]['citationItems'][$groupPosition][$variable];
                    }
                    break;
                case 'noteIndex':
                case 'index':
                case 'unsorted':
                    if (isset($this->data[$position][0]['properties']) == true
                        && isset($this->data[$position][0]['properties'][$variable]) == true) {
                        return $this->data[$position][0]['properties'][$variable];
                    }
                    break;
                case 'citationID':
                    if (isset($this->data[$position][0]['citationID']) == true) {
                        return $this->data[$position][0]['citationID'];
                    }
                    break;
                default:
                    if (isset($this->data[$position][0][$variable]) == true) {
                        return $this->data[$position][0][$variable];
                    }
                    break;
            }
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
        if ($name == 'citationID') {
            if (isset($this->data[$position]) == true) {
                $this->data[$position][0][$name]    =   $value;
                return true;
            }
        }

        return false;
    }


    /**
     * Changes the order of the actual group.
     *
     * @param array $group new order
     * @param bool $byKeys sort by the values of the keys or the actual values of the group-array
     * @return Citations
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
            for ($i = 0; $i < $this->getGroupLength(); $i++) {
                if ($id == $this->data[$this->position][0]['citationItems'][$i]['id']) {
                    $newOrder[] =   $this->data[$this->position][0]['citationItems'][$i];
                    break;
                }
            }
        }

        if (count($newOrder) > 0) {
            $this->data[$this->position][0]['citationItems']    =   $newOrder;
        }

        $this->groupPosition    =   0;
        return $this;
    }

    /**
     * Calculates the length of the actual group.
     *
     * @return int
     */
    protected function getGroupLength()
    {
        if (isset($this->data[$this->position]) == true) {
            return count($this->data[$this->position][0]['citationItems']);
        }

        return 0;
    }
}
