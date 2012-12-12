<?php
namespace Geissler\CSL\Macro;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Container;

/**
 * Call a macro.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Call implements Renderable
{
    /** @var string **/
    private $name;

    /**
     * Parses the Call configuration.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name =   (string) $name;
    }

    /**
     * Calls the macro and renders it.
     *
     * @param string|array $data
     * @return string|array
     */
    public function render($data)
    {
        return Container::getMacro($this->name)->render($data);
    }
}
