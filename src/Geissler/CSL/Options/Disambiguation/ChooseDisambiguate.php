<?php
namespace Geissler\CSL\Options\Disambiguation;

use Geissler\CSL\Interfaces\Disambiguate;
use Geissler\CSL\Options\Disambiguation\DisambiguateAbstract;
use Geissler\CSL\Container;

/**
 * If all disambiguation options fail render with choose disambiguate set to true.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class ChooseDisambiguate extends DisambiguateAbstract implements Disambiguate
{
    /**
     * Try to disambiguate the ambiguous values. If not possible, pass the values to the successor and try to
     * disambiguate with the successor. If possible, store ambiguous and disambiguated values.
     */
    public function disambiguate()
    {
        Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
        Container::getContext()->setChooseDisambiguateValue(true);
        $layout     =   Container::getContext()->get('layout', 'layout');
        $ambiguous  =   $this->getAmbiguous();

        foreach (array_keys($ambiguous) as $id) {
            $ambiguous[$id] =   $layout->renderById($id, '');
        }

        $this->store($this->getDisambiguate(), $ambiguous);
    }
}
