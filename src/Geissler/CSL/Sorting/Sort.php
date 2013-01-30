<?php
namespace Geissler\CSL\Sorting;

use Geissler\CSL\Container;
use Geissler\CSL\Sorting\Macro;
use Geissler\CSL\Sorting\Variable;
use Geissler\CSL\Interfaces\Sortable;

/**
 * Sorting.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Sort
{
    /** @var array **/
    private $keys;

    /**
     * Init sorting rules.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->keys =   array();

        foreach ($xml->children() as $child) {
            $sortingKey =   array('generate' => '', 'sort' => 'ascending');

            foreach ($child->attributes() as $name => $value) {
                switch ($name) {
                    case 'macro':
                        $sortingKey['generate'] =  new Macro($child);
                        break;
                    case 'variable':
                        $sortingKey['generate'] =   new Variable($child);
                        break;
                    case 'sort':
                        $sortingKey['sort'] =   (string) $value;
                        break;
                }
            }

            if ($sortingKey['generate'] instanceof Sortable) {
                $this->keys[]   =   $sortingKey;
            }
        }
    }

    /**
     * Sort the data in the given context.
     *
     * @param string $context
     * @return Sort
     */
    public function sort($context)
    {
        if (count($this->keys) == 0) {
            return false;
        }

        try {
            Container::getContext()->enter('sort', array('sort' => $this->keys[0]['sort']));
            if ($context == 'citation') {
                $this->citation();
            } else {
                $this->bibliography();
            }
            Container::getContext()->leave();

            return true;
        } catch (\ErrorException $error) {
            // ignore exceptions, because they are mainly thrown by incomplete tests
        }

        return false;
    }

    /**
     * Sort the citation items or if missing the bibliography data with the citation sorting keys.
     */
    private function citation()
    {
        if (Container::getCitationItem() !== false) {
            do {
                // ignore citation if properties => unsorted is set to true
                if (Container::getCitationItem()->get('unsorted') !== true) {
                    $sort   =   $this->generateCitationSortGroup();
                    Container::getCitationItem()->sortGroup($this->multiSort($sort), false);
                }
            } while (Container::getCitationItem()->next() == true);
        } else {
            $this->bibliography();
        }
    }

    /**
     * Generate the sorting arrays for the actual citation group.
     *
     * @return array
     */
    private function generateCitationSortGroup()
    {
        $sort   =   array();
        $length =   count($this->keys);

        for ($i = 0; $i < $length; $i++) {
            Container::getCitationItem()->moveToFirstInGroup();
            $position   =   0;
            do {
                $id         =   Container::getCitationItem()->get('id');
                Container::getData()->moveToId($id);

                if (isset($sort[$id]) == false) {
                    $sort[$id]  =   array();
                }

                $sort[$id][]  =   array(
                    $this->removeFormatting($this->keys[$i]['generate']->render(Container::getData()->get())),
                    $position++,
                    $this->keys[$i]['sort'] == 'ascending' ? 'asc' : 'desc',
                    $id
                );

            } while (Container::getCitationItem()->nextInGroup() == true);
        }

        return $sort;
    }

    /**
     * Sort the bibliography entries.
     */
    private function bibliography()
    {
        $order  =   $this->multiSort($this->generateBibliographySort());
        Container::getData()->sort($order);

        if (Container::getCitationItem() !== false) {
            Container::getCitationItem()->sort($order);
        }
    }

    /**
     * Generate the actual sorting data based on the bibliography data with the given sorting key.
     *
     * @return array
     */
    private function generateBibliographySort()
    {
        $sort   =   array();
        $length =   count($this->keys);

        for ($i = 0; $i < $length; $i++) {
            Container::getData()->moveToFirst();
            $position   =   0;
            do {
                $id =   Container::getData()->getVariable('id');
                if (isset($sort[$id]) == false) {
                    $sort[$id]  =   array();
                }

                $sort[$id][]  =   array(
                    $this->removeFormatting($this->keys[$i]['generate']->render(Container::getData()->get())),
                    $position++,
                    $this->keys[$i]['sort'] == 'ascending' ? 'asc' : 'desc',
                    $id
                );

            } while (Container::getData()->next() == true);
        }

        return $sort;
    }

    /**
     * Sort the values by all keys, until all values are sorted or no more sorting key ist left.
     *
     * @param array $sort
     * @return array
     */
    private function multiSort($sort)
    {
        $oldOrder   =   array_keys($sort);
        $sort       =   $this->removeNotToSort($sort);

        // test if nothing is to sort
        if (isset($sort[0][0][0]) == false) {
            return $oldOrder;
        }

        // sort
        uasort($sort, 'multiCompare');

        // get keys in order
        $keys   =   array();
        foreach ($sort as $entry) {
            $keys[] =   $entry[0][3];
        }

        return $keys;
    }

    /**
     * Remove sorting keys where all values are identical to avoid errors.
     *
     * @param array $sort
     * @return array
     */
    private function removeNotToSort($sort)
    {
        $sort       =   array_values($sort);
        $length     =   count($sort);
        $keys       =   count($sort[0]);
        $removeKeys =   array();
        for ($i = 0; $i < $keys; $i++) {
            $value  =   $sort[0][$i][0];
            $remove =   true;
            for ($j = 1; $j < $length; $j++) {
                if ($value !== $sort[$j][$i][0]) {
                    $remove =   false;
                    break;
                }
            }

            if ($remove == true) {
                $removeKeys[]   =   $i;
            }
        }

        for ($i = 0; $i < $length; $i++) {
            $values =   array();
            for ($j = 0; $j < $keys; $j++) {
                if (in_array($j, $removeKeys) == false) {
                    $values[]   =   $sort[$i][$j];
                }
            }
            $sort[$i]   =   $values;
        }

        return $sort;
    }

    /**
     * Remove not sortable values.
     *
     * @param string $value
     * @return string
     */
    private function removeFormatting($value)
    {
        return trim(str_replace(array('[', ']', '(', ')', '{', '}', ',', ';'), '', $value));
    }
}
