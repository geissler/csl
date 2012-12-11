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
    protected function validateVariable($variable)
    {
        if (Container::getData()->getVariable('label') == $variable) {
            return true;
        }

        return false;
    }
}
