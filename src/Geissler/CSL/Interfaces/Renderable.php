<?php
namespace Geissler\CSL\Interfaces;

/**
 * Defines the methods classes must implement to be renderd.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Renderable
{
    /**
     * Render the element.
     *
     * @param string $data
     * @return string
     */
    public function render($data);
}
