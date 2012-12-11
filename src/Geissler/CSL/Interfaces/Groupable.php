<?php
namespace Geissler\CSL\Interfaces;

use Geissler\CSL\Interfaces\Renderable;

/**
 * Defines the addtitional methods for rendering elements, which could be direct or as children of a direct
 * part-element part of Group element.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Groupable extends Renderable
{
    /**
     * If a Renderable object has tried to use a empty variable it returns true otherwise and when no variable
     * is used false. Needed for the Group element.
     *
     * @return boolean
     */
    public function hasAccessEmptyVariable();
}
