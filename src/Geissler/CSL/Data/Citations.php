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
        if ($groupPosition == false) {
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
                    if (isset($this->data[$position][0]['properties']) == true
                        && isset($this->data[$position][0]['properties'][$variable]) == true) {
                        return $this->data[$position][0]['properties'][$variable];
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
     * Changes the order of the actual group.
     *
     * @param array $group
     * @return Citations
     */
    public function sortGroup(array $group)
    {
        $newOrder   =   array();
        foreach (array_keys($group) as $id) {
            for ($i = 0; $i < $this->getGroupLength(); $i++) {
                if ($id == $this->data[$this->position][0]['citationItems'][$i]['id']) {
                    $newOrder[] =   $this->data[$this->position][0]['citationItems'][$i];
                    break;
                }
            }
        }

        $this->data[$this->position][0]['citationItems']    =   $newOrder;
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
        return count($this->data[$this->position][0]['citationItems']);
    }
}
