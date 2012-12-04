<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Container;

/**
 * Renders ordinals.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Ordinal
{
    /**
     * Renders number as a short ordinal.
     *
     * @param string $variable
     * @param boolean $checkDay Whether to check the limit-day-ordinals-to-day- option
     * @return string
     */
    public static function render($variable, $checkDay = false)
    {
        if ($checkDay == true
            && Container::getLocale()->getOptions('limit-day-ordinals-to-day-1') === true
            && (int) $variable > 1) {
                return $variable;
        }

        $ordinal    =  'ordinal-';
        if ((int) $variable < 10) {
            $ordinal .= '0' . (int) $variable;
        } else {
            $ordinal .= $variable;
        }

        $locale = Container::getLocale()->getTerms($ordinal);
        if ($locale !== null) {
            return (int) $variable . $locale;
        }

        return (int) $variable . Container::getLocale()->getTerms('ordinal');
    }

    /**
     * Renders a long-ordinal, with fallback to ordinal for numbers greater ten.
     *
     * @param string $variable
     * @return string
     */
    public static function renderLong($variable)
    {
        $ordinal    =  'long-ordinal-';
        if ((int) $variable <= 10) {
            $ordinal .= '0' . (int) $variable;
        } else {
            $ordinal .= $variable;
        }

        $long = Container::getLocale()->getTerms($ordinal);
        if ($long !== null) {
            return $long;
        }

        return self::render($variable);
    }
}
