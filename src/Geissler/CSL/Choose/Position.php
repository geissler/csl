<?php
namespace Geissler\CSL\Choose;

use Geissler\CSL\Interfaces\Chooseable;
use Geissler\CSL\Choose\ChooseableAbstract;
use Geissler\CSL\Container;

/**
 * Position.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Position extends ChooseableAbstract implements Chooseable
{

    /**
     * Tests whether the cite position matches the given positions
     *
     * @param string $variable
     * @return boolean
     * @todo implementation
     */
    protected function validateVariable($variable)
    {
        if (Container::getContext()->getName() == 'bibliography'
            || Container::getCitationItem() === false) {
            return false;
        }

        switch ($variable) {
            //  position of cites that are the first to reference an item
            case 'first':
                $length =   Container::getCitationItem()->getPosition();
                $actual =   Container::getCitationItem()->get('id');

                for ($i = 0; $i < $length; $i++) {
                    if (Container::getCitationItem()->getAtPosition('id', $i) == $actual) {
                        return false;
                    }
                }

                return true;
                break;
            case 'ibid':
            case 'ibid-with-locator':
            case 'subsequent':
                $position       =   Container::getCitationItem()->getPosition();
                $groupPosition  =   Container::getCitationItem()->getGroupPosition();

                if ($groupPosition > 0) {
                    if (Container::getCitationItem()->getAtPosition('id', $position, $groupPosition - 1) ==
                        Container::getCitationItem()->get('id')) {
                        return true;
                    }
                } else {
                    if (Container::getCitationItem()->getAtPosition('id', $position - 1, 0) ==
                        Container::getCitationItem()->get('id')
                        && Container::getCitationItem()->getAtPosition('id', $position - 1, 1) == null) {
                        return true;
                    }
                }
                break;
            case 'near-note':
                $maxDistance    =   Container::getContext()->getValue('nearNoteDistance', 'citation');
                $position       =   Container::getCitationItem()->getPosition();
                $id             =   Container::getCitationItem()->get('id');
                $start          =   0;
                if ($position > $maxDistance) {
                    $start  =   $position - $maxDistance;
                }

                for ($i = $start; $i < $position; $i++) {
                    $groupPosition  =   0;
                    $inGroup        =   true;
                    while ($inGroup == true) {
                        $actual =   Container::getCitationItem()->getAtPosition('id', $i, $groupPosition);
                        if ($actual == null) {
                            $inGroup    =   false;
                        } elseif ($actual == $id) {
                            return true;
                        } else {
                            $groupPosition++;
                        }
                    }
                }

                break;
        }

        return false;
    }
}
