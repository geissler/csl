<?php
namespace Geissler\CSL\Sorting;

use Geissler\CSL\Container;
use Geissler\CSL\Sorting\Macro;
use Geissler\CSL\Sorting\Variable;

/**
 * Description of Sort
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

            $this->keys[]   =   $sortingKey;
        }
    }

    public function sort($context)
    {
        Container::getContext()->enter('sort', array('sort' => $this->keys[0]['sort']));

        if ($context == 'citation') {
            $this->citation();
        } else {
            $this->bibliography();
        }

        Container::getContext()->leave();
        return $this;
        // citation or bibliography data durch laufen
        // sortier gruppe erstellen
        // sortieren der gruppe
        // sortierung zurück in Container schreiben
    }

    private function citation()
    {
        if (Container::getCitationItem() !== false) {
            $key    =   $this->keys[0]['generate'];
            do {
                $sort   =   $this->sortCitation($key, $this->keys[0]['sort']);

                if (array_values($sort) !== array_unique(array_values($sort))
                    && count($this->keys) > 1) {
                    // use next sorting key
                    Container::getCitationItem()->moveToFirstInGroup();
                    $newSort    =   $this->sortCitation($this->keys[1]['generate'], $this->keys[1]['sort']);
                }

                Container::getCitationItem()->sortGroup($sort);
            } while (Container::getCitationItem()->next() == true);
        } else {
            $this->bibliography();
        }
    }

    private function sortCitation($key, $direction)
    {
        $sort   =   array();

        do {
            $id =   Container::getCitationItem()->get('id');
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

    private function bibliography()
    {
        $sort   =   array();
        $key    =   $this->keys[0]['generate'];
        do {
            $sort[Container::getData()->getVariable('id')]  =   $key->render(Container::getData()->get());
        } while (Container::getData()->next() == true);

        if ($this->keys[0]['sort'] == 'ascending') {
            asort($sort);

        } else {
            arsort($sort);
        }

        if (array_values($sort) !== array_unique(array_values($sort))) {
            // use next sorting key
        }

        Container::getData()->sort(array_keys($sort));
    }
}
