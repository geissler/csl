<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Choose\ChooseableAbstract;
use Geissler\CSL\Rendering\Variable as GetVariable;

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
        $object =   new GetVariable(new \SimpleXMLElement('<variable variable="' . $variable . '"/>'));
        if ($object->render('') !== '') {
            return true;
        }

        return false;
    }
}
