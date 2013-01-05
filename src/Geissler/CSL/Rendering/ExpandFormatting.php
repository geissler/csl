<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;

/**
 * Short formatting options. Only defined in citeproc-test.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class ExpandFormatting implements Renderable
{
    /** @var array */
    private $formatting = array(
        '<sc>'  =>  '<font style="font-variant:small-caps">',
        '</sc>' =>  '</font>'
    );

    /**
     * Expand short formatting to full CSS formatting options.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        foreach ($this->formatting as $replace => $with) {
            $data   = str_replace($replace, $with, $data);
        }

        return $data;
    }
}
