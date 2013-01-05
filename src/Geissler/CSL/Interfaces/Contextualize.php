<?php
namespace Geissler\CSL\Interfaces;

use Geissler\CSL\Interfaces\Renderable;

/**
 * Renders a Renderable in a given context.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
interface Contextualize extends Renderable
{
    /**
     * Applies the context configuration to the object.
     *
     * @return \Geissler\CSL\Interfaces\Contextualize
     */
    public function apply();
}
