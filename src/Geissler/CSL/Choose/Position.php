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
     * Additionally activate different citation usage.
     *
     * @param string $variable
     * @param string $match
     */
    public function __construct($variable, $match = 'all')
    {
        parent::__construct($variable, $match);
        Container::getRendered()->setUseDifferentCitations(true);
    }

    /**
     * Tests whether the cite position matches the given positions
     *
     * @param string $variable
     * @return boolean
     */
    protected function validateVariable($variable)
    {
        if (Container::getContext()->getName() == 'bibliography'
            || Container::getContext()->in('bibliography') == true
        ) {
            return false;
        }

        if (Container::getCitationItem() === false) {
            if ($variable == 'first') {
                if (Container::getData()->getPosition() == 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        switch ($variable) {
            //  position of cites that are the first to reference an item
            case 'first':
                $length = Container::getCitationItem()->getPosition();
                $actual = Container::getCitationItem()->get('id');

                for ($i = 0; $i < $length; $i++) {
                    if (Container::getCitationItem()->getAtPosition('id', $i) == $actual) {
                        return false;
                    }
                }

                return true;
                break;
            case 'subsequent':
                $length = Container::getCitationItem()->getPosition();
                $actual = Container::getCitationItem()->get('id');

                for ($i = 0; $i < $length; $i++) {
                    if (Container::getCitationItem()->getAtPosition('id', $i) == $actual) {
                        return true;
                    }
                }
                break;
            case 'ibid':
                if ($this->isIbid() == true
                    && (Container::getCitationItem()->get('locator') == $this->getVariableForPrevious('locator')
                        || $this->isIbidWithLocator() == true)
                ) {
                    return true;
                }

                return false;
                break;
            case 'ibid-with-locator':
                return $this->isIbidWithLocator();
                break;
            case 'near-note':
                $nearNote = Container::getCitationItem()->get('near-note');
                if ($nearNote !== null) {
                    return $nearNote;
                }

                $maxDistance = Container::getContext()->getValue('nearNoteDistance', 'citation');
                $position = Container::getCitationItem()->getPosition();
                $id = Container::getCitationItem()->get('id');
                $start = 0;
                if ($position > $maxDistance) {
                    $start = $position - $maxDistance;
                }

                for ($i = $start; $i < $position; $i++) {
                    $groupPosition = 0;
                    $inGroup = true;
                    while ($inGroup == true) {
                        $actual = Container::getCitationItem()->getAtPosition('id', $i, $groupPosition);
                        if ($actual == null) {
                            $inGroup = false;
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

    /**
     * Test if the actual citation item has a ibid position.
     *
     * @return bool
     */
    private function isIbid()
    {
        $position = Container::getCitationItem()->getPosition();
        $groupPosition = Container::getCitationItem()->getGroupPosition();

        if ($groupPosition > 0) {
            if (Container::getCitationItem()->getAtPosition('id', $position, $groupPosition - 1) ==
                Container::getCitationItem()->get('id')
            ) {
                return true;
            }
        } elseif ($position > 0) {
            if (Container::getCitationItem()->getAtPosition('id', $position - 1, 0) ==
                Container::getCitationItem()->get('id')
                && Container::getCitationItem()->getAtPosition('id', $position - 1, 1) == null
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Test if the actual citation item has a ibid-with-locator position.
     *
     * @return bool
     */
    private function isIbidWithLocator()
    {
        if ($this->isIbid() == true
            && Container::getCitationItem()->get('locator') !== $this->getVariableForPrevious('locator')
            && Container::getCitationItem()->get('locator') !== null
        ) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve a variable from a previous cite.
     *
     * @param string $variable
     * @return int|string
     */
    private function getVariableForPrevious($variable)
    {
        $position = Container::getCitationItem()->getPosition();
        $groupPosition = Container::getCitationItem()->getGroupPosition();

        if ($groupPosition > 0) {
            return Container::getCitationItem()->getAtPosition($variable, $position, $groupPosition - 1);
        } elseif ($position > 0) {
            return Container::getCitationItem()->getAtPosition($variable, $position - 1, 0);
        }

        return '';
    }
}
