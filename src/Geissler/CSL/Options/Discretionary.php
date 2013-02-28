<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Container;
use Geissler\CSL\Rendering\Value;

/**
 * Discretionary.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Discretionary
{
    /**
     * Use only some child elements for rendering if author-only or suppress-author is used.
     *
     * @param array $children
     * @return array
     */
    public function getRenderClasses(array $children)
    {
        if (Container::getCitationItem() !== false) {
            if (Container::getCitationItem()->get('author-only') == 1) {
                $render =   array();

                foreach ($children as $child) {
                    if (in_array('Geissler\CSL\Interfaces\Variable', class_implements($child)) == true
                        && $child->isAccessingVariable('author') == true) {
                        $render[]  =   $child;
                    }
                }

                if (count($render) == 0) {
                    foreach ($children as $child) {
                        if (in_array('Geissler\CSL\Interfaces\Variable', class_implements($child)) == true
                            && $child->isAccessingVariable('citation-number') == true) {
                            $render[]   =   new Value(new \SimpleXMLElement('<value value="Reference " />'));
                            $render[]   =   $child;
                        }
                    }
                }

                return $render;
            } elseif (Container::getCitationItem()->get('suppress-author') == 1) {
                return $this->suppressAuthor($children);
            }
        }

        return $children;
    }

    /**
     * Retrieve only those rendering elements, which are not using an author or editor variable.
     *
     * @param array $children
     * @return array
     */
    private function suppressAuthor(array $children)
    {
        $render =   array();

        foreach ($children as $child) {
            if (($child instanceof \Geissler\CSL\Rendering\Group) == true) {
                $render =   array_merge($render, $this->suppressAuthor($child->getChildren()));
            } elseif ((in_array('Geissler\CSL\Interfaces\Variable', class_implements($child)) == true
                    && $child->isAccessingVariable('author') == false
                    && $child->isAccessingVariable('editor') == false)
                || ($child instanceof \Geissler\CSL\Interfaces\Variable) == false) {
                $render[]  =   $child;
            }
        }

        return $render;
    }
}
