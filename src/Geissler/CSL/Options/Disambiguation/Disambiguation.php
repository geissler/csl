<?php
namespace Geissler\CSL\Options\Disambiguation;

use Geissler\CSL\Container;
use Geissler\CSL\Options\Disambiguation\Chain;
use Geissler\CSL\Helper\ArrayData;

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
    /** @var \Geissler\CSL\Rendering\Layout */
    private $layout;
    /** @var \Geissler\CSL\Names\Names */
    private $names;

    public function solve()
    {
        Container::getContext()->enter('disambiguation');
        $this->layout   =   Container::getContext()->get('layout', 'layout');
        $this->names    =   $this->layout->getChildElement('\Geissler\CSL\Names\Names');

        // if "et-al-subsequent-min" or "et-al-subsequent-use-first" are used, use the first citation
        // to disambiguate the values (see disambiguate_BasedOnEtAlSubsequent.txt)
        if (Container::getRendered()->getUseDifferentCitations() == true) {
            $citations      =   Container::getRendered()->getAllByType('firstCitation');
        } else {
            $citations      =   Container::getRendered()->getAllByType('citation');
        }

        if (is_object($this->names) == true) {
            $this->sorted   =   array_keys($citations);
            asort($citations);
            $this->disambiguateByName($citations);

        } else {
            $this->disambiguateByCitationLabel($citations);
        }
    }

    /**
     * Disambiguate citations if citation-label is used but no name.
     *
     * @param array $citations
     */
    private function disambiguateByCitationLabel($citations)
    {
        $ambiguous   =   ArrayData::ambiguous($citations);
        new Chain($ambiguous);
    }

    /**
     * Disambiguate citations by the name properties.
     *
     * @param array $citations
     */
    private function disambiguateByName($citations)
    {
        $identical  =   array();
        $last       =   false;
        $lastNames  =   false;
        foreach ($citations as $id => $citation) {
            Container::getData()->moveToId($id);
            $actualNames    =   $this->names->render('');

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

        // run through disambiguation chain
        new Chain($rePositioned);

        if (Container::getRendered()->getUseDifferentCitations() == true) {
            if (Container::getContext()->isChooseDisambiguationActive() == true) {
                // re-render citation with choose disambiguate validates to true
                foreach (array_keys($rePositioned) as $id) {
                    $rendered    =   Container::getRendered()->getById($id);
                    Container::getRendered()->updateCitation($id, false, $rendered['citation']);
                }
            } elseif ($reRender == true) {
                // if entries are disambiguated by the names, the full first cite must be re-rendered
                Container::getContext()->setIgnoreEtAlSubsequent(true);

                foreach (array_keys($rePositioned) as $id) {
                    $rendered    =   Container::getRendered()->getById($id);
                    Container::getRendered()->updateCitation(
                        $id,
                        $this->layout->renderById($id, ''),
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
}
