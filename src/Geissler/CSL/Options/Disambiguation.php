<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Options\Disambiguate;
use Geissler\CSL\Container;

class Disambiguation
{
    public function solve()
    {
        $citations  =   Container::getRendered()->getAllByType('citation');
        asort($citations);

        $disambiguation =   new Disambiguate();
        $identical      =   array();
        $last           =   '';
        foreach ($citations as $id => $citation) {
            if ($last === '') {
                $identical  =   array($id => $citation);
                $last       =   $citation;
            } elseif ($last == $citation) {
                $identical[$id] =   $citation;
            } else {
                $disambiguation->solve($identical);
                $identical  =   array($id => $citation);
                $last       =   $citation;
            }
        }
    }
}
