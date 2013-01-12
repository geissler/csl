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
            return $this;
        }

        Container::getContext()->enter('sort', array('sort' => $this->keys[0]['sort']));
        if ($context == 'citation') {
            $this->citation();
        } else {
            $this->bibliography();
        }
        Container::getContext()->leave();

        return $this;
    }

    /**
     * Sort the citation items or if missing the bibliography data with the citation sorting keys.
     */
    private function citation()
    {
        if (Container::getCitationItem() !== false) {
            do {
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

                Container::getCitationItem()->sortGroup($sort);
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
        $sort       =   $this->generateBibliographySort($this->keys[0]['generate'], $this->keys[0]['sort']);
        $iteration  =   0;

        while ($iteration + 1 < count($this->keys)) {
            if ($this->keys[$iteration]['sort'] == 'ascending') {
                $firstDirection =   SORT_ASC;
            } else {
                $firstDirection =   SORT_DESC;
            }

            $iteration++;
            $nextSort    =   $this->generateBibliographySort(
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

        Container::getData()->sort(array_keys($sort));
    }

    /**
     * Generate the actual sorting data based on the bibliography data with the given sorting key.
     *
     * @param \Geissler\CSL\Interfaces\Sortable $key
     * @param string $direction
     * @return array
     */
    private function generateBibliographySort(Sortable $key, $direction)
    {
        Container::getData()->moveToFirst();
        $sort   =   array();

        do {
            $sort[Container::getData()->getVariable('id')]  =   $key->render(Container::getData()->get());
        } while (Container::getData()->next() == true);

        if ($direction == 'ascending') {
            asort($sort);

        } else {
            arsort($sort);
        }

        return $sort;
    }
}
