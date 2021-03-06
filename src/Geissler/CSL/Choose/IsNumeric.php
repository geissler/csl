<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Choose\ChooseableAbstract;
use Geissler\CSL\Container;

/**
 * Is numeric test.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class IsNumeric extends ChooseableAbstract implements Chooseable
{
    /**
     * Tests if the variable is numeric.
     *
     * @param string $variable
     * @return boolean
     */
    protected function validateVariable($variable)
    {
        if (preg_match(
            '/^[0-9]+([ ]{0,1}[&|\-|,][ ]{0,1}[0-9]+)*$|^[A-z]{0,1}[0-9]+[A-z]{0,1}$|^[0-9]+[A-z]{2,3}$/',
            Container::getData()->getVariable($variable)
        ) == 1) {

            return true;
        }

        return false;
    }
}
