<?php
namespace Geissler\CSL\Options\Disambiguation;

use Geissler\CSL\Options\Disambiguation\AddNames;
use Geissler\CSL\Options\Disambiguation\AddYearSuffix;
use Geissler\CSL\Options\Disambiguation\AddGivenName;
use Geissler\CSL\Options\Disambiguation\AddHiddenGivenName;
use Geissler\CSL\Options\Disambiguation\Store;
use Geissler\CSL\Container;

/**
 * Disambiguate ambiguous cites.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @licence MIT
 */
class Disambiguation
{
    /** @var array */
    private $sorted;

    public function solve()
    {
        Container::getContext()->enter('disambiguation');
        $names  =   Container::getContext()->get('layout', 'layout')->getChildElement('\Geissler\CSL\Names\Names');

        if (is_object($names) == false) {
            return false;
        }

        // if "et-al-subsequent-min" or "et-al-subsequent-use-first" are used, use the first citation
        // to disambiguate the values (see disambiguate_BasedOnEtAlSubsequent.txt)
        if (Container::getRendered()->getUseDifferentCitations() == true) {
            $citations      =   Container::getRendered()->getAllByType('firstCitation');
        } else {
            $citations      =   Container::getRendered()->getAllByType('citation');
        }

        $this->sorted   =   array_keys($citations);
        asort($citations);


        $identical  =   array();
        $last       =   false;
        $lastNames  =   false;
        foreach ($citations as $id => $citation) {
            Container::getData()->moveToId($id);
            $actualNames    =   $names->render('');

            if ($last === false) {
                $identical      =   array();
                $lastNames      =   $actualNames;
                $last           =   $citation;

                $identical[]    =   array(
                    'id'        =>  $id,
                    'citation'  =>  $citation,
                    'position'  =>  $this->getSortedPosition($id),
                    'names'     =>  $actualNames
                );

            } elseif ($last == $citation
                || $lastNames == $actualNames) {
                $identical[]    =   array(
                    'id'        =>  $id,
                    'citation'  =>  $citation,
                    'position'  =>  $this->getSortedPosition($id),
                    'names'     =>  $actualNames
                );
            } else {
                if (count($identical) > 1) {
                    $this->disambiguateIdentical($identical);
                }

                $last           =   $citation;
                $lastNames      =   $actualNames;
                $identical      =   array();
                $identical[]    =   array(
                    'id'    =>  $id,
                    'citation'  =>  $citation,
                    'position'  =>  $this->getSortedPosition($id),
                    'names'     =>  $actualNames
                );

            }
        }

        if (count($identical) > 1) {
            $this->disambiguateIdentical($identical);
        }

        Container::getContext()->leave();
    }

    private function getSortedPosition($id)
    {
        return array_search($id, $this->sorted);
    }

    private function disambiguateIdentical($identical)
    {
        $reRender       =   false;
        $rePositioned   =   $this->rePositioningCitations($identical);
        $values         =   array_values($rePositioned);

        if ($values[0] !== $values[1]) {
            $reRender       =   true;
            $rePositioned   =   $this->rePositioningCitations($identical, 'names');
        }

        $this->createChain($rePositioned);

        if (Container::getRendered()->getUseDifferentCitations() == true) {
            if (Container::getContext()->getUseChooseDisambiguate() == true) {
                // re-render citation with choose disambiguate validates to true
                foreach (array_keys($rePositioned) as $id) {
                    $rendered    =   Container::getRendered()->getById($id);
                    Container::getRendered()->updateCitation($id, false, $rendered['citation']);
                }
            } elseif ($reRender == true) {
                // if entries are disambiguated by the names, the full first cite must be re-rendered
                Container::getContext()->setIgnoreEtAlSubsequent(true);
                $layout =   Container::getContext()->get('layout', 'layout');
                foreach (array_keys($rePositioned) as $id) {
                    $rendered    =   Container::getRendered()->getById($id);
                    Container::getRendered()->updateCitation(
                        $id,
                        $layout->renderById($id, ''),
                        $rendered['firstCitation']
                    );
                }

                Container::getContext()->setIgnoreEtAlSubsequent(false);
            } else {
                // if firstCitation and citation in ambiguous mode are identical, copy first citation to citation
                foreach (array_keys($rePositioned) as $id) {
                    $rendered    =   Container::getRendered()->getById($id);
                    if (isset($rendered['citation']) == true) {
                        Container::getRendered()->updateCitation(
                            $id,
                            $rendered['firstCitation'],
                            $rendered['citation']
                        );
                    }
                }
            }
        }
    }

    private function rePositioningCitations($data, $field = 'citation')
    {
        $positions  =   array();
        foreach ($data as $key => $entry) {
            $positions[$key]    =   $entry['position'];
        }

        array_multisort($positions, SORT_ASC, $data);
        $solve  =   array();
        foreach ($data as $entry) {
            $solve[$entry['id']]    =   $entry[$field];
        }

        return $solve;
    }

    /**
     * Create a chain of objects to disambiguate the ambiguous values. The first element tries to disambiguate the
     * values. If it fails, the ambiguous values are passed to the next chain element. The chain stops, if all
     * ambiguous values are disambiguated or if no succeeding chain element exists.
     * @param array $ambiguous
     */
    public function createChain(array $ambiguous)
    {
        $store  =   new Store();
        $store->setAmbiguous($ambiguous);
        $chain  =   false;

        // step 3
        if (Container::getContext()->getValue('disambiguateAddYearSuffix', 'citation') == true) {
            $addYear    =   new AddYearSuffix();
            $addYear->setStore($store);
            $chain  =   $addYear;
        }

        // step 2, a
        // if disambiguate-add-names is set to "true", then the names still hidden as a result of
        // et-al abbreviation after the disambiguation attempt of disambiguate-add-names are
        // added one by one to all members of a set of ambiguous cites
        if (Container::getContext()->getValue('disambiguateAddNames', 'citation') === true
            && Container::getContext()->getValue('disambiguateAddGivenname', 'citation') == true) {
            Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
            $addHidden  =   new AddHiddenGivenName();
            $addHidden->setStore($store);

            if ($chain !== false) {
                $addHidden->setSuccessor($chain);
            }

            $chain  =   $addHidden;
        }

        // step 2
        if (Container::getContext()->getValue('disambiguateAddGivenname', 'citation') == true) {
            $addGiveName    =   new AddGivenName();
            $addGiveName->setStore($store);

            if ($chain !== false) {
                $addGiveName->setSuccessor($chain);
            }
            $chain  =   $addGiveName;
        }

        // step 1
        if (Container::getContext()->getValue('disambiguateAddNames', 'citation') === true) {
            $addNames   =   new AddNames();
            $addNames->setStore($store);

            if ($chain !== false) {
                $addNames->setSuccessor($chain);
            }
            $chain  =   $addNames;
        }

        if ($chain !== false) {
            $chain->setAmbiguous($ambiguous)
                ->setDisambiguate(array())
                ->disambiguate();
        }
    }
}
