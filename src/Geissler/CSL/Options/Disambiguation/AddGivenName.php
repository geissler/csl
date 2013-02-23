<?php
namespace Geissler\CSL\Options\Disambiguation;

use Geissler\CSL\Interfaces\Disambiguate;
use Geissler\CSL\Options\Disambiguation\DisambiguateAbstract;
use Geissler\CSL\Helper\ArrayData;
use Geissler\CSL\Container;

/**
 * Disambiguate by adding given names.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class AddGivenName extends DisambiguateAbstract implements Disambiguate
{
    /** @var array */
    protected $tmpAmbiguous;
    /** @var array */
    protected $tmpDisambiguate;
    /** @var array */
    private $replaceNames = array();

    /**
     * Try to disambiguate the ambiguous values. If not possible, pass the values to the successor and try to
     * disambiguate with the successor. If possible, store ambiguous and disambiguated values.
     */
    public function disambiguate()
    {
        Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
        $this->tmpAmbiguous     =   $this->getAmbiguous();
        $this->tmpDisambiguate  =   $this->getDisambiguate();

        if (is_array($this->getAmbiguous()) == true
            && $this->addGivenName($this->getAmbiguous()) == true) {
            $this->store($this->tmpDisambiguate, $this->tmpAmbiguous);
        } else {
            $this->succeed($this->tmpDisambiguate, $this->tmpAmbiguous);
        }
    }

    /**
     * Render names in all possible forms an pick the right one.
     *
     * @param $ambiguous
     * @return bool
     */
    protected function addGivenName(array $ambiguous)
    {
        $layout         =   Container::getContext()->get('layout', 'layout');
        $originalFull   =   array();

        foreach (array_keys($ambiguous) as $id) {
            $originalFull[$id]  =   $layout->renderById($id, '');
        }

        // render standard form
        $standard   =   $this->renderNames($ambiguous, true);

        // render long form
        Container::getContext()->setDisambiguationOptions(
            'Geissler\CSL\Names\Name',
            array(
                'form'  => 'long',
            )
        );

        // long form with spaces in given names
        $longGivenNames =   $this->renderNames($ambiguous, true);

        Container::getContext()->setDisambiguationOptions(
            'Geissler\CSL\Names\Name',
            array(
                'trimGivenName' =>  true
            )
        );
        $longForm       =   $this->renderNames($ambiguous, true);

        // render not initialized form
        Container::getContext()->removeDisambiguationOption('Geissler\CSL\Names\Name', 'trimGivenName');
        Container::getContext()->setDisambiguationOptions(
            'Geissler\CSL\Names\Name',
            array(
                'initialize' => false
            )
        );
        // given names with normal spaces
        $notInitTrimNames   =   $this->renderNames($ambiguous, true);

        Container::getContext()->setDisambiguationOptions(
            'Geissler\CSL\Names\Name',
            array(
                'trimGivenName' =>  true
            )
        );
        $notInitForm    =   $this->renderNames($ambiguous, true);

        $disambiguate       =   array();
        $numberOfAmbiguous  =   count($standard);
        $disambiguationRule =   Container::getContext()->getValue('givennameDisambiguationRule', 'citation');
        if ($numberOfAmbiguous > 0
            && is_array(current($standard)) == true) {

            $numberOfNames  =   count(current($standard));
            for ($i = 0; $i < $numberOfNames; $i++) {
                $actualStandard =   $this->createArrayWithDataFromPosition($standard, $i);
                $actualLong     =   $this->createArrayWithDataFromPosition($longForm, $i);
                $actualInit     =   $this->createArrayWithDataFromPosition($notInitForm, $i);
                $actualLongTrim =   $this->createArrayWithDataFromPosition($longGivenNames, $i);
                $actualInitTrim =   $this->createArrayWithDataFromPosition($notInitTrimNames, $i);

                // check which standard forms are already ambiguous
                $standardAmbiguous  =   ArrayData::disambiguate($actualStandard);
                if (count($standardAmbiguous) > 0) {
                    foreach (array_keys($standardAmbiguous) as $id) {
                        if (isset($actualLong[$id]) == true) {
                            unset($actualLong[$id]);
                        }

                        if (isset($actualInit[$id]) == true) {
                            unset($actualInit[$id]);
                        }
                    }
                }

                // disambiguate by long form
                $longAmbiguous  =   ArrayData::disambiguate($actualLong);
                if (count($longAmbiguous) > 0) {
                    foreach ($longAmbiguous as $id => $name) {
                        if (isset($disambiguate[$id]) == false) {
                            $disambiguate[$id]  =   array();
                        }

                        $disambiguate[$id][]  =   array(
                            'position'  =>  $i,
                            'value'     =>  $actualLongTrim[$id],
                            'form'      =>  'long'
                        );

                        if (isset($actualInit[$id]) == true) {
                            unset($actualInit[$id]);
                        }
                    }
                }

                // disambiguate by initialize set to false
                if ($disambiguationRule !== 'all-names-with-initials'
                    && $disambiguationRule !== 'primary-name-with-initials') {
                    $initAmbiguous  =   ArrayData::disambiguate($actualInit);
                    if (count($initAmbiguous) > 0) {
                        foreach ($initAmbiguous as $id => $name) {
                            if (isset($disambiguate[$id]) == false) {
                                $disambiguate[$id]  =   array();
                            }

                            $disambiguate[$id][]  =   array(
                                'position'  =>  $i,
                                'value'     =>  $actualInitTrim[$id],
                                'form'      =>  'init'
                            );
                        }
                    }
                }

                $stop   =   false;
                switch ($disambiguationRule) {
                    case 'all-names':
                        break;
                    case 'by-cite':
                        if (count($disambiguate) > 0) {
                            $stop   =   true;
                        }
                        break;
                    case 'primary-name':
                    case 'primary-name-with-initials':
                        $stop   =   true;
                        break;
                }

                if ($stop == true) {
                    break;
                }
            }
        }

        // change disambiguated names
        $highestPosition    =   0;
        $form               =   '';
        foreach ($disambiguate as $id => $changes) {
            $originalValue  =   $originalFull[$id];

            // replace previously modified names
            if (isset($this->replaceNames[$id]) == true) {
                foreach ($this->replaceNames[$id] as $replace => $replaceBy) {
                    $originalValue  =   str_replace($replace, $replaceBy, $originalValue);
                }
            }

            foreach ($changes as $options) {
                $replace        =   $standard[$id][$options['position']];
                $originalValue  =   str_replace($replace, $options['value'], $originalValue);

                if ($highestPosition < $options['position']) {
                    $highestPosition    =   $options['position'];
                    $form               =   $options['form'];
                }
            }

            $this->tmpDisambiguate[$id]    =   $originalValue;
            unset($this->tmpAmbiguous[$id]);
        }

        // other ambiguous cites muss at least contain the same number of names
        if ($highestPosition > 0) {
            Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
            $etAl   =   Container::getContext()->getValue('etAlUseFirst', 'citation');
            Container::getContext()->setDisambiguationOptions(
                'Geissler\CSL\Names\Name',
                array(
                    'etAlUseFirst'  =>  $etAl + $highestPosition,)
            );

            foreach (array_keys($this->tmpAmbiguous) as $id) {
                $this->tmpAmbiguous[$id]   =   $layout->renderById($id, '');

                // replace ambiguous names at position of disambiguate value
                foreach ($disambiguate as $changes) {
                    foreach ($changes as $options) {
                        $replace        =   $standard[$id][$options['position']];

                        if ($form == 'long') {
                            $replaceBy  =   $longForm[$id][$options['position']];
                        } else {
                            $replaceBy  =   $notInitForm[$id][$options['position']];
                        }

                        $this->tmpAmbiguous[$id]   =   str_replace($replace, $replaceBy, $this->tmpAmbiguous[$id]);

                        // store name replacement
                        if (isset($this->replaceNames[$id]) == false) {
                            $this->replaceNames[$id]    =   array();
                        }
                        $this->replaceNames[$id][$replace]  =   $replaceBy;
                    }
                }
            }
        }

        if (count($this->tmpAmbiguous) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Create an array with only the names at the given position.
     *
     * @param array $data
     * @param integer $position
     * @return array
     */
    private function createArrayWithDataFromPosition(array $data, $position)
    {
        $return =   array();
        foreach ($data as $id => $names) {
            $return[$id] = $names[$position];
        }

        return $return;
    }
}
