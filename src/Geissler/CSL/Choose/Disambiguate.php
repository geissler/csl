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
     * Activate choose disambiguation usage.
     */
    public function __construct()
    {
        Container::getContext()->activateChooseDisambiguation();
    }

    /**
     * Validates the variable if the rendering context is disambiguation.
     *
     * @return boolean
     */
    public function validate()
    {
        return Container::getContext()->getChooseDisambiguateValue();
    }
}
