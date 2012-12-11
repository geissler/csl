<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Choose\ChooseableAbstract;
use Geissler\CSL\Container;

/**
 * IsUncertainDate.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class IsUncertainDate extends ChooseableAbstract implements Chooseable
{
    /**
     * A date is uncertain if it contains the key "circa".
     *
     * @param string $variable
     * @return boolean
     */
    protected function validateVariable($variable)
    {
        $date   = Container::getData()->getVariable($variable);

        if (is_array($date) == true
            && isset($date['circa']) == true) {
            return true;
        }

        return false;
    }
}
