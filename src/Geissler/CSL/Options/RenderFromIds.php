<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Interfaces\Optional;
use Geissler\CSL\Container;

/**
 * RenderFromIds.php.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class RenderFromIds implements Optional
{
    /**
     * Apply the optional modification.
     *
     * @param array $data
     * @return array|string
     */
    public function apply(array $data)
    {
        $delimiter  =   Container::getContext()->get('delimiter', 'layout');
        $layout     =   Container::getContext()->get('layout', 'layout');

        // move to starting position for citations and citations items
        $citationData   =   false;
        if (Container::getCitationItem() !== false) {
            $citationData   =   true;
            Container::getCitationItem()->moveToFirst();
        }

        // remove all additional temporary disambiguation options
        Container::getContext()->clearDisambiguationOptions();

        // replace item ids by disambiguate cite
        $length =   count($data);
        for ($i = 0; $i < $length; $i++) {
            if (is_array($data[$i]) == true) {
                $innerLength    =   count($data[$i]);
                for ($j = 0; $j < $innerLength; $j++) {
                    // re-render citation if missing
                    $actualCitation =   Container::getRendered()->getCitationById($data[$i][$j]);
                    if ($actualCitation == false) {
                        Container::getContext()->enter('disambiguation');
                        $data[$i][$j]   =   $layout->renderJustActualEntry('');
                        Container::getContext()->leave();
                    } else {
                        $data[$i][$j]   =   $actualCitation;
                    }

                    // Add delimiter at end if not ending with a dot
                    // (see affix_SuppressDelimiterCharsWhenFullStopInSuffix.txt)
                    if ($j < $innerLength - 1) {
                        if (preg_match('/\.$/', $data[$i][$j]) == 0) {
                            $data[$i][$j] .=  $delimiter;
                        } else {
                            $data[$i][$j] .= ' ';
                        }
                    }

                    // move to next in group
                    if ($citationData == true) {
                        Container::getCitationItem()->nextInGroup();
                    }
                }
            } else {
                // re-render citation if missing
                $actualCitation =   Container::getRendered()->getCitationById($data[$i]);
                if ($actualCitation == false) {
                    Container::getContext()->enter('disambiguation');
                    $data[$i]   =   $layout->renderJustActualEntry('');
                    Container::getContext()->leave();
                } else {
                    $data[$i]   =   $actualCitation;
                }
            }

            if ($citationData == true) {
                Container::getCitationItem()->next();
            }
        }

        return $data;
    }
}
