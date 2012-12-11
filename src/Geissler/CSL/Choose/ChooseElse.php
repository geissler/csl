<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Interfaces\Groupable;
use Geissler\CSL\Choose\ChooseIf;

/**
 * Else Element of Choose.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class ChooseElse extends ChooseIf implements Renderable, Groupable, Chooseable
{
    /**
     * An else statement will always validate to true.
     *
     * @return boolea
     */
    public function validate()
    {
        return true;
    }
}
