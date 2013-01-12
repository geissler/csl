<?php
namespace Geissler\CSL\Interfaces;

/**
 * Variable testing.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Chooseable
{
    /**
     * Validates the variable.
     *
     * @return boolean
     */
    public function validate();
}
