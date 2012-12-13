<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;

/**
 * Short formating options. Only defined in citeproc-test.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class ExpandFormating implements Renderable
{
    private $formating = array(
        '<sc>'  =>  '<span style="font-variant:small-caps;">',
        '</sc>' =>  '</span>'
    );

    /**
     * Expand short formatings to full CSS formating options.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        foreach ($this->formating as $replace => $with) {
            $data   = str_replace($replace, $with, $data);
        }

        return $data;
    }
}
