<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Choose\ChooseableAbstract;
use Geissler\CSL\Container;

/**
 * Variable.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Variable extends ChooseableAbstract implements Chooseable
{
    /**
     * Tests whether the item matches the given types.
     *
     * @param string $variable
     * @return boolean
     */
    protected function validateVariable($variable)
    {
        if (Container::getData()->getVariable($variable) !== null) {
            return true;
        }

        return false;
    }
}
