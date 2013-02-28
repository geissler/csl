<?php
namespace Geissler\CSL\Interfaces;

/**
 * Test for the usage of variables.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Variable
{
    /**
     * Tests if the element or an child element is accessing the variable with the given name.
     *
     * @param string $name
     * @return boolean
     */
    public function isAccessingVariable($name);
}
