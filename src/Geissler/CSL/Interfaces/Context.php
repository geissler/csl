<?php
namespace Geissler\CSL\Interfaces;

/**
 * Rendering context (citation or bibliography).
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Context
{
    /**
     * Returns the rendering context (citation or bibliography).
     *
     * @return stirng
     */
    public function getName();

    /**
     * Returns the standard values for this context.
     *
     * @return  array
     */
    public function getOptions();
}
