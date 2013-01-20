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
            $length =   count($this->keys);

            do {
                $sort   =   array();
                for ($i = 0; $i < $length; $i++) {
                    $keys   =   $this->generateCitationSort($this->keys[$i]['generate'], $this->keys[$i]['sort']);

                    $j = 0;
                    foreach ($keys as $k => $value) {
                        if (isset($sort[$j]) == false) {
                            $sort[$j]   =   array('id' => $k);
                        }
                        $sort[$j][$i]   =   array('value' => $value, 'sort' => $this->keys[$i]['sort']);
                        $j++;
                    }
                }

                /*
                $sort   =   $this->generateCitationSort($this->keys[0]['generate'], $this->keys[0]['sort']);
                $iteration  =   0;

                while (isset($this->keys[$iteration + 1]) == true
                    && ($this->keys[$iteration + 1]['generate'] instanceof Sortable) == true) {

                    if ($this->keys[$iteration]['sort'] == 'ascending') {
                        $firstDirection =   SORT_ASC;
                    } else {
                        $firstDirection =   SORT_DESC;
                    }

                    $iteration++;
                    $nextSort    =   $this->generateCitationSort(
                        $this->keys[$iteration]['generate'],
                        $this->keys[$iteration]['sort']
                    );

                    if ($this->keys[$iteration]['sort'] == 'ascending') {
                        $nextDirection  =   SORT_ASC;
                    } else {
                        $nextDirection =   SORT_DESC;
                    }

                    array_multisort($sort, $firstDirection, $nextSort, $nextDirection, $sort);
                }
                */

                Container::getCitationItem()->sortGroup($this->multiSort($sort));
            } while (Container::getCitationItem()->next() == true);
        } else {
            $this->bibliography();
        }
    }

    /**
     * Generate the actual sorting data based on the citation data with the given sorting key.
     *
     * @param \Geissler\CSL\Interfaces\Sortable $key
     * @param string $direction
     * @return array
     */
    private function generateCitationSort(Sortable $key, $direction)
    {
        $sort   =   array();

        do {
            $id         =   Container::getCitationItem()->get('id');
            Container::getData()->moveToId($id);
            $sort[$id]  =   $key->render(Container::getData()->get());
        } while (Container::getCitationItem()->nextInGroup() == true);

        if ($direction == 'ascending') {
            asort($sort);

        } else {
            arsort($sort);
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

            do {
                $id =   Container::getData()->getVariable('id');
                if (isset($sort[$id]) == false) {
                    $sort[$id]  =   array('id' => $id);
                }

                $sort[$id][$i]  =   array(
                    'value' =>  $this->keys[$i]['generate']->render(Container::getData()->get()),
                    'sort'  =>  $this->keys[$i]['sort']
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
        // sort
        $sort   =   array_values($sort);
        mergesort($sort, 'multiSort');

        // get keys in order
        $keys   =   array();
        $length =   count($sort);
        for ($i = 0; $i < $length; $i++) {
            $keys[] =   $sort[$i]['id'];
        }

        return $keys;
    }
}
