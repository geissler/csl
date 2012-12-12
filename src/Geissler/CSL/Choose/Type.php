<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Choose\ChooseableAbstract;
use Geissler\CSL\Container;

/**
 * Type.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Type extends ChooseableAbstract implements Chooseable
{
    /**
     * Tests whether the item matches the given types.
     *
     * @param string $variable
     * @return boolean
     */
    protected function validateVariable($variable)
    {
        if (Container::getData()->getVariable('type') == $variable) {
            return true;
        }

        return false;
    }
}
