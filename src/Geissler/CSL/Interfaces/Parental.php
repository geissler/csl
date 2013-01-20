<?php
namespace Geissler\CSL\Interfaces;

/**
 * Retrieve a child element.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Parental
{
    /**
     * Retrieve the first child element matching the given class name.
     *
     * @param string $class full, namespace aware class name
     * @return object
     */
    public function getChildElement($class);

    /**
     * Modify the first child element.
     *
     * @param string $class full, namespace aware class name
     * @param \SimpleXMLElement $xml
     * @return object
     */
    public function modifyChildElement($class, \SimpleXMLElement $xml);

    /**
     * Tests if the element or an child element is accessing the variable with the given name.
     *
     * @param string $name
     * @return boolean
     */
    public function isAccessingVariable($name);
}
