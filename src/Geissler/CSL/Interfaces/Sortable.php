<?php
namespace Geissler\CSL\Interfaces;

interface Sortable
{
    /**
     * Renders the values for the sorting.
     *
     * @param string|data $data
     * @return array
     */
    public function render($data);
}
