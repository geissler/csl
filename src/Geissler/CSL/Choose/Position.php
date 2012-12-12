<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Choose\ChooseableAbstract;
use Geissler\CSL\Container;

/**
 * Position.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Position extends ChooseableAbstract implements Chooseable
{
    /**
     * Tests whether the cite position matches the given positions
     *
     * @param string $variable
     * @return boolean
     * @todo implementation
     */
    protected function validateVariable($variable)
    {
        return false;
    }
}
