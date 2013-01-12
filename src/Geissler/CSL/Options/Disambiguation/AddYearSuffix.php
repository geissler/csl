<?php
namespace Geissler\CSL\Options\Disambiguation;

use Geissler\CSL\Interfaces\Disambiguate;
use Geissler\CSL\Options\Disambiguation\DisambiguateAbstract;
use Geissler\CSL\Container;

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

    /**
     * Try to disambiguate the ambiguous values. If not possible, pass the values to the successor and try to
     * disambiguate with the successor. If possible, store ambiguous and disambiguated values.
     */
    public function disambiguate()
    {
        Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
        $this->tmpDisambiguate  =   $this->getDisambiguate();
        $this->tmpAmbiguous     =   $this->getAmbiguous();

        $this->addYearSuffix();
        $this->store($this->tmpDisambiguate, $this->tmpAmbiguous);
    }

    /**
     * Add an alphabetic year-suffix to ambiguous cites.
     *
     * @return void
     */
    private function addYearSuffix()
    {
        if (array_values($this->tmpAmbiguous) > array_unique(array_values($this->tmpAmbiguous))) {
            $layout                 =   Container::getContext()->get('layout', 'layout');
            $useYearSuffixVariable  =   $layout->isAccessingVariable('year-suffix');
            $suffix =   'a';

            foreach (array_keys($this->tmpAmbiguous) as $id) {
                $addYearSuffix = true;

                // test if year is rendered
                if (preg_match('/([0-9]{2,4})/', $this->tmpAmbiguous[$id]) == 0
                    && Container::getContext()->getUseChooseDisambiguate() == true) {
                    $reRendered =   $layout->renderById($id, '');

                    if (isset($this->tmpAmbiguous[$id]) == true
                        && $reRendered !== $this->tmpAmbiguous[$id]) {
                        $this->tmpDisambiguate[$id]    =   $reRendered;
                        unset($this->tmpAmbiguous[$id]);
                        $addYearSuffix  =   false;
                    }
                }

                if ($addYearSuffix == true) {
                    // store year-suffix variable
                    Container::getData()->moveToId($id);
                    Container::getData()->setVariable('year-suffix', $suffix);

                    if ($useYearSuffixVariable == true) {
                        $this->tmpDisambiguate[$id]    =   $layout->renderById($id, '');
                        unset($this->tmpAmbiguous[$id]);
                    } else {
                        $withYearSuffix =   preg_replace('/([0-9]{2,4})/', '$1' . $suffix, $this->tmpAmbiguous[$id]);
                        $withYearSuffix =   str_replace('&#38' . $suffix . ';', '&#38;', $withYearSuffix);
                        $this->tmpDisambiguate[$id]    =   $withYearSuffix;
                        unset($this->tmpAmbiguous[$id]);
                    }

                    $suffix++;
                }
            }
        }
    }
}
