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
     * Validates the varibale.
     *
     * @return boolean
     */
    public function validate();
}
