<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Interfaces\Optional;
use Geissler\CSL\Container;

/**
 * Cite Grouping.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class CiteGrouping implements Optional
{
    /** @var string */
    private $citeGroupDelimiter;
    /** @var bool */
    private $active = false;

    /**
     * Active the usage if cite grouping.
     *
     * @param boolean $active
     * @return CiteGrouping
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Activates cite grouping and specifies the delimiter for cites within a cite group.
     *
     * @param string $citeGroupDelimiter
     * @return CiteGrouping
     */
    public function setCiteGroupDelimiter($citeGroupDelimiter)
    {
        $this->citeGroupDelimiter = $citeGroupDelimiter;
        return $this;
    }

    /**
     * With cite grouping, cites in in-text citations with identical rendered names are grouped together.
     *
     * @param array $data
     * @return array
     */
    public function apply(array $data)
    {
        $names  =   Container::getContext()->get('layout', 'layout')->getChildElement('\Geissler\CSL\Names\Names');

        if ($this->active == false
            || is_object($names) == false
            || Container::getCitationItem() === false) {
            return $data;
        }

        Container::getCitationItem()->moveToFirst();
        $delimiter  =   Container::getContext()->get('delimiter', 'layout');
        if (isset($this->citeGroupDelimiter) == false) {
            $this->citeGroupDelimiter   =   $delimiter;
            $this->citeGroupDelimiter   =   ', ';
        }

        $length     =   count($data);
        $newData    =   array();

        for ($i = 0; $i < $length; $i++) {
            // get all names in citation group
            $namesAsArray   =   array();
            do {
                Container::getData()->moveToId(Container::getActualId());
                $namesAsArray[] =   $names->renderAsArray('');
            } while (Container::getCitationItem()->nextInGroup() == true);

            $newData[$i]    =   array();
            $citeLength     =   count($namesAsArray);
            for ($j = 0; $j < $citeLength; $j++) {
                $actualName =   $namesAsArray[$j][0];

                // check if name already used
                if ($actualName !== '') {
                    $actualGroup    =   array($data[$i][$j]);
                    $groupPosition  =   0;

                    for ($k = $j + 1; $k < $citeLength; $k++) {
                        if ($actualName == $namesAsArray[$k][0]) {
                            // replace delimiter in previous group entry with cite group delimiter
                            $actualGroup[$groupPosition]['delimiter']   =   $this->citeGroupDelimiter;
                            $actualGroup[]          =   $data[$i][$k];
                            $namesAsArray[$k][0]    =   '';
                            $groupPosition++;

                            // Add delimiter to values without one
                            if ($actualGroup[$groupPosition]['delimiter'] == '') {
                                $actualGroup[$groupPosition]['delimiter']   =   $delimiter;
                            }
                        }
                    }

                    $newData[$i]    =   array_merge($newData[$i], $actualGroup);
                }
            }

            // remove delimiter from last entry in group
            $newData[$i][count($newData[$i]) - 1]['delimiter']   =   '';

            Container::getCitationItem()->next();
        }

        return $newData;
    }
}
