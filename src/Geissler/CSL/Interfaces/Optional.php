<?php
namespace Geissler\CSL\Interfaces;

/**
 * An additional optionally method to modify the rendered result of a citation or bibliography.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Optional
{
    /**
     * Apply the optional modification.
     *
     * @param array $data
     * @return array|string
     */
    public function apply(array $data);
}
