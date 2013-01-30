<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Choose\ChooseableAbstract;
use Geissler\CSL\Container;

/**
 * Locator.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Locator extends ChooseableAbstract implements Chooseable
{
    /**
     * Test if label is equal to the variable.
     *
     * @param string $variable
     * @return bool
     */
    protected function validateVariable($variable)
    {
        if (Container::getData()->getVariable('label') == $variable
            || (is_object(Container::getCitationItem()) == true
                && Container::getCitationItem()->get('label') == $variable)) {
            return true;
        }

        return false;
    }
}
