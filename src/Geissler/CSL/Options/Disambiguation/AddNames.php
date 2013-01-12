<?php
namespace Geissler\CSL\Options\Disambiguation;

use Geissler\CSL\Interfaces\Disambiguate;
use Geissler\CSL\Options\Disambiguation\DisambiguateAbstract;
use Geissler\CSL\Container;

/**
 * Disambiguate by adding names.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class AddNames extends DisambiguateAbstract implements Disambiguate
{
    /** @var array */
    private $tmpAmbiguous;
    /** @var array */
    private $tmpDisambiguate;
    /** @var integer */
    private $etAl;

    /**
     * Try to disambiguate the ambiguous values. If not possible, pass the values to the successor and try to
     * disambiguate with the successor. If possible, store ambiguous and disambiguated values.
     */
    public function disambiguate()
    {
        $this->tmpAmbiguous     =   $this->getAmbiguous();
        $this->tmpDisambiguate  =   array();

        // init disambiguation
        $this->etAl     =   Container::getContext()->getValue('etAlUseFirst', 'citation');
        if ($this->etAl == '') {
            $this->etAl =   0;
        }

        $namesAdded             =   $this->addNames($this->tmpAmbiguous);

        // use same number of et-al-first in all citations
        if ($namesAdded['etAl'] > 0) {
            Container::getContext()->setDisambiguationOptions(
                'Geissler\CSL\Names\Name',
                array('etAlUseFirst' => $namesAdded['etAl'])
            );

            $layout =   Container::getContext()->get('layout', 'layout');
            foreach (array_keys($this->tmpAmbiguous) as $id) {
                $this->tmpAmbiguous[$id]   =   $layout->renderById($id, '');
            }
        }

        if (count(array_unique(array_values($this->tmpAmbiguous))) > 1) {
            $this->addNamesWithDuplication($namesAdded['ambiguous']);

            // group identical "disambiguated" values
            asort($this->tmpAmbiguous);
            $last   =   '';
            $groups =   array();
            foreach ($this->tmpAmbiguous as $id => $value) {
                if ($last !== $value) {
                    if (isset($group) == true) {
                        $groups[]   =   $group;
                    }
                    $group  =   array($id => $value);
                    $last   =   $value;
                } else {
                    $group[$id] =   $value;
                }
            }
            $groups[]   =   $group;

            // restore order inside the splitter groups
            $sort   =   array();
            $length =   count($groups);
            foreach (array_keys($this->getAmbiguous()) as $id) {
                for ($i = 0; $i < $length; $i++) {
                    if (array_key_exists($id, $groups[$i]) == true) {
                        if (isset($sort[$i]) == false) {
                            $sort[$i]   =   array();
                        }

                        $sort[$i][$id]  =   $groups[$i][$id];
                        break;
                    }
                }
            }

            $this->setDisambiguate($this->tmpDisambiguate);
            $store  =   false;
            if (is_object($this->getSuccessor()) == false) {
                $store  =   true;
            }

            // call successor if exists or store actual values
            for ($i = 0; $i < $length; $i++) {
                if ($store == false) {
                    $this->succeed($this->tmpDisambiguate, $sort[$i]);
                } else {
                    $this->store($this->tmpDisambiguate, $sort[$i]);
                }
            }
        } elseif (is_object($this->getSuccessor()) == true) {
            $this->succeed($this->tmpDisambiguate, $this->tmpAmbiguous);
        } else {
            $this->store($this->tmpDisambiguate, $this->tmpAmbiguous);
        }
    }

    /**
     * Add by et-al suppressed names to disambiguate the cites.
     *
     * @param array $ambiguous
     * @return array
     */
    private function addNames($ambiguous)
    {
        $last           =   '';
        $original       =   $this->renderNames($ambiguous);
        $highestEtAl    =   $this->etAl;
        $names          =   array();

        do {
            $this->etAl++;
            Container::getContext()->setDisambiguationOptions(
                'Geissler\CSL\Names\Name',
                array('etAlUseFirst' => $this->etAl)
            );

            $values =   $this->renderNames($ambiguous);

            // keep disambiguate values
            foreach ($this->getEntries($values, false) as $id => $newValue) {
                $this->tmpDisambiguate[$id]    =   str_replace($original[$id], $newValue, $this->tmpAmbiguous[$id]);
                unset($this->tmpAmbiguous[$id]);
                $highestEtAl    =   $this->etAl;
                $names[$id]     =   $newValue;
            }

            $ambiguous      =   $this->getEntries($values, true);
            if ($last == implode('', $ambiguous)) {
                break;
            } else {
                $last   =   implode('', $ambiguous);
            }
        } while (1 == 1);

        return array(
            'etAl'      =>  $highestEtAl,
            'ambiguous' =>  $ambiguous,
            'names'     =>  $names
        );
    }

    /**
     * Try to disambiguate group of identical cites.
     *
     * @param $ambiguous
     * @return void
     */
    private function addNamesWithDuplication($ambiguous)
    {
        // reset et-al
        $this->etAl     =   Container::getContext()->getValue('etAlUseFirst', 'citation');
        if ($this->etAl == '') {
            $this->etAl =   0;
        }

        // add names to one of each identical group
        $original           =   $this->renderNames($ambiguous);
        $unique             =   array_unique(array_values($ambiguous));
        $uniqueAmbiguous    =   array();
        foreach ($unique as $value) {
            $uniqueAmbiguous[array_search($value, $ambiguous)]  =   $value;
        }
        $namesAdded =   $this->addNames($uniqueAmbiguous);

        // copy disambiguated values to identical
        foreach ($ambiguous as $id => $value) {
            if (isset($uniqueAmbiguous[$id]) == false) {
                $otherId    =   array_search($value, $uniqueAmbiguous);
                $this->tmpAmbiguous[$id]   =   str_replace(
                    $original[$id],
                    $namesAdded['names'][$otherId],
                    $this->tmpAmbiguous[$id]
                );
            } else {
                $this->tmpAmbiguous[$id]   =   $this->tmpDisambiguate[$id];
                unset($this->tmpDisambiguate[$id]);
            }
        }
    }
}
