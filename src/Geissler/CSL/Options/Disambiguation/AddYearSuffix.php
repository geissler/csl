<?php
namespace Geissler\CSL\Options\Disambiguation;

use Geissler\CSL\Interfaces\Disambiguate;
use Geissler\CSL\Options\Disambiguation\DisambiguateAbstract;
use Geissler\CSL\Container;
use Geissler\CSL\Helper\ArrayData;

/**
 * Disambiguate by adding an alphabetic year-suffix.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class AddYearSuffix extends DisambiguateAbstract implements Disambiguate
{
    /** @var array */
    private $tmpAmbiguous;
    /** @var array */
    private $tmpDisambiguate;
    /** @var string */
    private $regExp;

    /**
     * Try to disambiguate the ambiguous values. If not possible, pass the values to the successor and try to
     * disambiguate with the successor. If possible, store ambiguous and disambiguated values.
     */
    public function disambiguate()
    {
        Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
        $this->tmpDisambiguate  =   $this->getDisambiguate();
        $this->tmpAmbiguous     =   $this->getAmbiguous();
        $firstDifferent         =   false;

        // disambiguate years and missing years
        $this->regExp   =  '/([0-9]{2,4}';
        if (Container::getLocale()->getTerms('do date', 'short') !== null) {
            $this->regExp   .=  '|' . Container::getLocale()->getTerms('do date', 'short');
        } elseif (Container::getLocale()->getTerms('do date', 'short') !== null) {
            $this->regExp   .=  '|' . Container::getLocale()->getTerms('do date');
        }
        $this->regExp   .=  ')/';

        // disambiguate only where names and year are identical
        foreach ($this->tmpAmbiguous as $id => $name) {
            if (preg_match($this->regExp, $name) == 0) {
                $citation   =   Container::getRendered()->getFirstById($id);

                if (preg_match($this->regExp, $citation) == 1
                    && preg_match('/^' . preg_quote($this->tmpAmbiguous[$id], '/') . '/', $citation) == 1) {
                    $this->tmpAmbiguous[$id] =  $citation;
                }
            }
        }

        $ambiguous =   ArrayData::ambiguous($this->tmpAmbiguous);
        if (count($ambiguous) > 0) {
            $this->tmpAmbiguous =   $ambiguous;
            $this->addYearSuffix();
        } elseif ($firstDifferent == true) {
            // test if second citation is ambiguous
            $this->tmpAmbiguous =   $this->getAmbiguous();
            $this->addYearSuffix();
        } elseif (is_array($this->tmpDisambiguate) == true) {
            $this->tmpDisambiguate  =   array_merge($this->tmpDisambiguate, $this->getAmbiguous());
        } else {
            $this->tmpDisambiguate  =   $this->getAmbiguous();
        }

        if (count($ambiguous) > 0) {
            // store already disambiguated values
            $succeedAmbiguous    =   array();
            if ($this->getAmbiguous() == $ambiguous) {
                $succeedAmbiguous    =   $ambiguous;
            }
            $this->succeed($this->getDisambiguate(), $succeedAmbiguous);
        }
    }

    /**
     * Add an alphabetic year-suffix to ambiguous cites.
     *
     * @return void
     */
    private function addYearSuffix()
    {
        // test if year is rendered, if not try if year is rendered through choose disambiguate
        $layout =   Container::getContext()->get('layout', 'layout');
        if (preg_match($this->regExp, current($this->tmpAmbiguous)) == 0
            && Container::getContext()->isChooseDisambiguationActive() == true) {
            Container::getContext()->setChooseDisambiguateValue(true);

            foreach (array_keys($this->tmpAmbiguous) as $id) {
                $reRendered =   $layout->renderById($id, '');

                if (isset($this->tmpAmbiguous[$id]) == true
                    && $reRendered !== $this->tmpAmbiguous[$id]) {
                    $this->tmpAmbiguous[$id]    =   $reRendered;
                }
            }
        }

        if (array_values($this->tmpAmbiguous) > array_unique(array_values($this->tmpAmbiguous))) {
            $useYearSuffix  =   $layout->isAccessingVariable('year-suffix');
            $suffix         =   'a';

            foreach (array_keys($this->tmpAmbiguous) as $id) {
                Container::getData()->moveToId($id);
                $actualSuffix   =   Container::getData()->getVariable('year-suffix');

                if ($actualSuffix === null) {
                    // store year-suffix variable
                    Container::getData()->setVariable('year-suffix', $suffix);
                    $actualSuffix   =   $suffix;
                }

                if (Container::getCitationItem() !== false) {
                    $cites  =   Container::getCitationItem()->getWithIds(array($id));
                    foreach ($cites as $entry) {
                        Container::getCitationItem()->moveTo($id, $entry['citationID']);

                        if ($useYearSuffix == true) {
                            Container::getRendered()->clearById($id);
                            Container::getRendered()->set($id, $entry['citationID'], $layout->renderById($id, ''));
                        } else {
                            Container::getRendered()->set(
                                $id,
                                $entry['citationID'],
                                $this->addYearSuffixToValue($this->tmpAmbiguous[$id], $actualSuffix)
                            );
                        }
                    }
                } else {
                    $cites = Container::getRendered()->getAllById();
                    foreach ($cites as $itemId => $value) {
                        if ($itemId == $id) {
                            if ($useYearSuffix == true) {
                                Container::getRendered()->clearById($id);
                                Container::getRendered()->set($id, 0, $layout->renderById($id, ''));
                            } else {
                                $newValue   =   $this->addYearSuffixToValue($value, $actualSuffix);
                                Container::getRendered()->set($id, 0, $newValue);
                                $this->tmpDisambiguate[$id] =   $newValue;
                            }
                        }
                    }
                }

                unset($this->tmpAmbiguous[$id]);
                $suffix++;
            }
        }

    }

    /**
     * Add a suffix to a rendered year.
     *
     * @param string $value
     * @param string $suffix
     * @return string
     */
    private function addYearSuffixToValue($value, $suffix)
    {
        $value = preg_replace($this->regExp, '$1' . $suffix, $value);
        return str_replace('&#38' . $suffix . ';', '&#38;', $value);
    }
}
