<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Choose\ChooseableAbstract;
use Geissler\CSL\Container;

/**
 * Disambiguate.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Disambiguate implements Chooseable
{
    /**
     * Validates the variable if the rendering context is disambiguation.
     *
     * @return boolean
     */
    public function validate()
    {
        Container::getContext()->setUseChooseDisambiguate(true);
        return Container::getContext()->in('disambiguation');
    }
}
