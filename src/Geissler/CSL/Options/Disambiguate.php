<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Container;

/**
 * @todo description
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Disambiguate
{
    /** @var \Geissler\CSL\Rendering\Layout */
    private $layout;
    /** @var \Geissler\CSL\Names\Names */
    private $names;
    /** @var integer */
    private $etAl;
    /** @var string */
    private $id;
    /** @var string */
    private $value;
    /** @var array */
    private $disambiguate;
    /** @var array */
    private $other;

    public function solve(array $ambiguous)
    {

    }

    public function solveOld($value, $id)
    {
        // check if entry already rendered
        $rendered   =   Container::getRendered()->getById($id);
        if ($rendered !== false
            && isset($rendered['citation']) == true) {
            return $rendered['citation'];
        }

        // create ambiguous entry
        $this->id       =   $id;
        $this->value    =   $value;
        $this->layout   =   Container::getContext()->get('layout');
        $this->names    =   $this->layout->getChildElement('\Geissler\CSL\Names\Names');
        if ($this->names === false) {
            return $value;
        }
        $ambiguous      =   $this->names->render('');
        Container::getRendered()->addAmbiguous($id, $ambiguous);

        $this->disambiguate =   false;
        if (Container::getRendered()->isAmbiguous($ambiguous, $id) == true) {
            // find identical cite
            $this->other    =   Container::getRendered()->getOtherByValue($ambiguous, 'ambiguous', $this->id);

            // init disambiguation
            $this->etAl     =   Container::getContext()->getValue('etAlUseFirst', 'citation');
            if ($this->etAl == '') {
                $this->etAl =   0;
            }

            $this->disambiguate();

            // replace disambiguated value if applicable
            if ($ambiguous !== $this->disambiguate
                && $this->disambiguate !== false
                && $this->disambiguate !== '') {
                $this->value  =   str_replace($ambiguous, $this->disambiguate, $this->value);
            }
        } elseif (Container::getContext()->getValue('etAlSubsequentMin', 'citation') !== ''
            || Container::getContext()->getValue('etAlSubsequentUseFirst', 'citation') !== '') {
            $this->addYearSuffixToSubsequentEtAl();
        }

        // store new citation
        Container::getRendered()->addCitation($this->id, $this->value);

        if ($this->disambiguate !== false) {
            Container::getRendered()->addDisambiguation($this->id, $this->disambiguate);
        }

        // reset name rendering
        Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');

        // update other value
        $this->updateOtherValue();

        return $this->value;
    }

    private function disambiguate()
    {
        $disambiguated  =   false;

        // step 1
        if (Container::getContext()->getValue('disambiguateAddNames', 'citation') === true) {
            do {
                $last           =   $this->disambiguate;
                $disambiguated  =   $this->addNames();
            } while($last !== $this->disambiguate && $disambiguated == false);
        }

        // step 2
        if (Container::getContext()->getValue('disambiguateAddGivenname', 'citation') == true
            && $disambiguated == false) {
            Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
            do {
                $last           =   $this->disambiguate;
                $disambiguated  =   $this->addGivenName();
            } while($last !== $this->disambiguate && $disambiguated == false);
        }

        // if disambiguate-add-names is set to "true", then the names still hidden as a result of
        // et-al abbreviation after the disambiguation attempt of disambiguate-add-names are
        // added one by one to all members of a set of ambiguous cites
        if ($disambiguated == false
            && Container::getContext()->getValue('disambiguateAddNames', 'citation') === true
            && Container::getContext()->getValue('disambiguateAddGivenname', 'citation') == true) {
            $disambiguated  =   $this->addHiddenNames();
        }

        // step 3
        if (Container::getContext()->getValue('disambiguateAddYearSuffix', 'citation') == true
            && $disambiguated == false) {
            $reRender   =   true;

            // disambiguate by adding a suffix
            if (Container::getContext()->getValue('disambiguateAddNames', 'citation') === true
                && Container::getContext()->getValue('disambiguateAddGivenname', 'citation') == true
                && $this->disambiguate !== $this->other['disambiguation']
            ) {
                $reRender   =   false;
            } else {
                Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
                $this->disambiguate =   false;
                unset($this->other['disambiguation']);
            }

            var_dump($this->value);
            $this->addYearSuffix($reRender);
        }

        return $disambiguated;
    }

    private function addNames()
    {
        $this->etAl++;
        Container::getContext()->setDisambiguationOptions(
            'Geissler\CSL\Names\Name',
            array('etAlUseFirst' => $this->etAl)
        );

        $newValue   =   $this->names->render('');

        // render other identical value
        Container::getData()->moveToId($this->other['id']);
        $otherValue =   $this->names->render('');
        Container::getData()->moveToId($this->id);

        /*
        if ($this->disambiguate === false) {
            Container::getRendered()->addDisambiguation($this->other['id'], $otherValue);
            $this->other['disambiguation']  =   $otherValue;

            Container::getRendered()->addDisambiguation($this->id, $newValue);
            $this->disambiguate =   $newValue;
        }
        */
        if ($newValue !== $otherValue
            && Container::getRendered()->isAmbiguous($this->disambiguate, $this->id) == false) {
            $this->other['disambiguation']  =   $otherValue;
            $this->disambiguate             =   $newValue;
            return true;
        }

        return false;
    }

    private function addGivenName($modifyLastEntry = false)
    {
        // render standard form
        $newValue   =   $this->names->render('');
        $newNames   =   $this->names->renderAsArray('');

        Container::getData()->moveToId($this->other['id']);
        $otherValue =   $this->names->render('');
        $otherNames =   $this->names->renderAsArray('');
        Container::getData()->moveToId($this->id);

        // render long form
        Container::getContext()->setDisambiguationOptions('Geissler\CSL\Names\Name', array('form' => 'long'));
        $newNamesLong   =   $this->names->renderAsArray('');

        // render other identical value
        Container::getData()->moveToId($this->other['id']);
        $otherNamesLong =   $this->names->renderAsArray('');
        Container::getData()->moveToId($this->id);

        // render not initialized form
        Container::getContext()->setDisambiguationOptions(
            'Geissler\CSL\Names\Name',
            array('initialize' => false)
        );
        $newNamesInit   =   $this->names->renderAsArray('');

        // render other identical value
        Container::getData()->moveToId($this->other['id']);
        $otherNamesInit =   $this->names->renderAsArray('');
        Container::getData()->moveToId($this->id);

        $length         =   count($newNames);
        $newReplace     =   array();
        $newReplaceBy   =   array();
        $otherReplace   =   array();
        $otherReplaceBy =   array();

        switch (Container::getContext()->getValue('givennameDisambiguationRule', 'citation')) {
            case 'all-names':
                for ($i = 0; $i < $length; $i++) {
                    if ($newNames[$i] == $otherNames[$i]) {
                        if ($newNamesLong[$i] !== $otherNamesLong[$i]) {
                            $newReplace[]       =   $newNames[$i];
                            $newReplaceBy[]     =   $newNamesLong[$i];

                            $otherReplace[]     =   $otherNames[$i];
                            $otherReplaceBy[]   =   $otherNamesLong[$i];
                        } elseif ($newNamesInit[$i] !== $otherNamesInit[$i]) {
                            $newReplace[]       =   $newNames[$i];
                            $newReplaceBy[]     =   $newNamesInit[$i];

                            $otherReplace[]     =   $otherNames[$i];
                            $otherReplaceBy[]   =   $otherNamesInit[$i];
                        } elseif ($modifyLastEntry == true
                            && $i == $length - 1) {
                            $newReplace[]       =   $newNames[$i];
                            $newReplaceBy[]     =   $newNamesLong[$i];

                            $otherReplace[]     =   $otherNames[$i];
                            $otherReplaceBy[]   =   $otherNamesLong[$i];
                        }
                    }
                }
                break;
            case 'all-names-with-initials':
                // No disambiguation attempt is made when initialize-with is not set or
                // when initialize is set to "false"
                if ((Container::getContext()->getValue('initializeWith', 'citation') !== ''
                    && Container::getContext()->getValue('initialize', 'citation') !== false)
                    || ($this->names->getName()->getPropertyValue('initializeWith') !== null
                        && $this->names->getName()->getPropertyValue('initialize') !== false)) {

                    for ($i = 0; $i < $length; $i++) {
                        if ($newNames[$i] == $otherNames[$i]) {
                            if ($newNamesLong[$i] !== $otherNamesLong[$i]) {
                                $newReplace[]       =   $newNames[$i];
                                $newReplaceBy[]     =   $newNamesLong[$i];

                                $otherReplace[]     =   $otherNames[$i];
                                $otherReplaceBy[]   =   $otherNamesLong[$i];
                            } elseif ($modifyLastEntry == true
                                && $i == $length - 1) {
                                $newReplace[]       =   $newNames[$i];
                                $newReplaceBy[]     =   $newNamesLong[$i];

                                $otherReplace[]     =   $otherNames[$i];
                                $otherReplaceBy[]   =   $otherNamesLong[$i];
                            }
                        }
                    }
                }
                break;
            case 'primary-name':
                if ($length > 0) {
                    if ($newNames[0] == $otherNames[0]) {
                        if ($newNamesLong[0] !== $otherNamesLong[0]) {
                            $newReplace[]       =   $newNames[0];
                            $newReplaceBy[]     =   $newNamesLong[0];

                            $otherReplace[]     =   $otherNames[0];
                            $otherReplaceBy[]   =   $otherNamesLong[0];
                        } elseif ($newNamesInit[0] !== $otherNamesInit[0]) {
                            $newReplace[]       =   $newNames[0];
                            $newReplaceBy[]     =   $newNamesInit[0];

                            $otherReplace[]     =   $otherNames[0];
                            $otherReplaceBy[]   =   $otherNamesInit[0];
                        }
                    }
                }
                break;
            case 'primary-name-with-initials':
                if ($length > 0) {
                    if ($newNames[0] == $otherNames[0]
                        && $newNamesLong[0] !== $otherNamesLong[0]) {
                        $newReplace[]       =   $newNames[0];
                        $newReplaceBy[]     =   $newNamesLong[0];

                        $otherReplace[]     =   $otherNames[0];
                        $otherReplaceBy[]   =   $otherNamesLong[0];
                    }
                }
                break;
            default:
                // Only ambiguous names in ambiguous cites are affected, and disambiguation stops after
                // the first name that eliminates cite ambiguity
                for ($i = 0; $i < $length; $i++) {
                    if ($newNames[$i] == $otherNames[$i]) {
                        if ($newNamesLong[$i] !== $otherNamesLong[$i]) {
                            $newReplace[]       =   $newNames[$i];
                            $newReplaceBy[]     =   $newNamesLong[$i];

                            $otherReplace[]     =   $otherNames[$i];
                            $otherReplaceBy[]   =   $otherNamesLong[$i];
                            break;
                        } elseif ($newNamesInit[$i] !== $otherNamesInit[$i]) {
                            $newReplace[]       =   $newNames[$i];
                            $newReplaceBy[]     =   $newNamesInit[$i];

                            $otherReplace[]     =   $otherNames[$i];
                            $otherReplaceBy[]   =   $otherNamesInit[$i];
                            break;
                        } elseif ($modifyLastEntry == true
                            && $i == $length - 1) {
                            $newReplace[]       =   $newNames[$i];
                            $newReplaceBy[]     =   $newNamesLong[$i];

                            $otherReplace[]     =   $otherNames[$i];
                            $otherReplaceBy[]   =   $otherNamesLong[$i];
                            break;
                        }
                    }
                }
                break;
        }

        // replace identical values by un-identical
        $newValue   =   str_replace($newReplace, $newReplaceBy, $newValue);
        $otherValue =   str_replace($otherReplace, $otherReplaceBy, $otherValue);

        if ($newValue !== $otherValue
            || $modifyLastEntry == true) {
            $this->other['disambiguation']  =   $otherValue;
            $this->disambiguate             =   $newValue;
            return true;
        }

        return false;
    }

    private function addHiddenNames()
    {

        $this->etAl =   Container::getContext()->getValue('etAlUseFirst', 'citation');
        $length     =   count($this->names->renderAsArray(''));

        do {
            // add additional name hidden by et-al
            Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
            $this->disambiguate =   false;
            $this->addNames();

            $disambiguated  =   $this->addGivenName();
        } while($disambiguated == false && $this->etAl <= $length);

        if ($disambiguated == false) {
            Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
            Container::getContext()->setDisambiguationOptions(
                'Geissler\CSL\Names\Name',
                array('etAlUseFirst' => $this->etAl)
            );
            $this->addGivenName(true);
        }

        return $disambiguated;


        /*
        Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
        $newNames   =   $this->names->renderAsArray('');
        Container::getData()->moveToId($this->other['id']);
        $otherNames =   $this->names->renderAsArray('');
        Container::getData()->moveToId($this->id);

        var_dump($newNames);
        var_dump($otherNames);

        // add additional name hidden by et-al
        $newEtAl            =   Container::getContext()->getValue('etAlUseFirst', 'citation');
        $this->disambiguate =   false;
        $this->addNames();

        // rendered hidden names
        Container::getContext()->setDisambiguationOptions('Geissler\CSL\Names\Name', array('form' => 'long'));
        $newAddNames   =   $this->names->renderAsArray('');
        Container::getData()->moveToId($this->other['id']);
        $otherAddNames =   $this->names->renderAsArray('');
        Container::getData()->moveToId($this->id);

        // replace new rendered names by name with given name value
        $length =   count($newNames);
        for ($i = $newEtAl - 1; $i < $length; $i++) {
            $this->disambiguate =   str_replace($newNames[$i], $newAddNames[$i], $this->disambiguate);
            $this->other['disambiguation']  =   str_replace(
                $otherNames[$i],
                $otherAddNames[$i],
                $this->other['disambiguation']
            );
        }

        $target     =   $this->other['citation'];
        $replace    =   str_replace($this->other['ambiguous'], $this->other['disambiguation'], $target);
        Container::getRendered()->addCitation($this->other['id'], $replace);
        Container::getRendered()->addReplace($target, $replace);
        $this->other['citation']    =   $replace;
        */
    }

    /**
     * Add an alphabetic year-suffix to ambiguous cites.
     *
     * @return boolean
     */
    private function addYearSuffix($reRender = true)
    {
        $useYearSuffixVariable  =   $this->layout->isAccessingVariable('year-suffix');
        if (isset($this->other['suffix']) == false) {
            // add suffix to first cite
            $suffix     =   'a';

            // store year-suffix variable
            Container::getData()->moveToId($this->other['id']);
            Container::getData()->setVariable('year-suffix', $suffix);
            Container::getData()->moveToId($this->id);

            if ($reRender == true) {
                $withYearSuffix =   $this->layout->renderById($this->other['id'], '');
            } else {
                $withYearSuffix =   str_replace(
                    $this->other['ambiguous'],
                    $this->other['disambiguation'],
                    $this->other['citation']
                );
            }

            var_dump($this->disambiguate);
            var_dump($this->other);
            var_dump('ohter year ' . $withYearSuffix);

            if ($useYearSuffixVariable == false) {
                $withYearSuffix =   preg_replace('/([0-9]{2,4})/', '$1' . $suffix, $withYearSuffix);
                $withYearSuffix =   str_replace('&#38' . $suffix . ';', '&#38;', $withYearSuffix);
            }

            $this->other['citation']    =   $withYearSuffix;
            $this->other['suffix']      =   $suffix;
        } else {
            $suffix =   Container::getRendered()->findLastSuffix($this->other['ambiguous']);
        }

        // add suffix to actual cite and store suffix for next cite
        $suffix++;
        Container::getData()->setVariable('year-suffix', $suffix);
        Container::getRendered()->addSuffix($this->id, $suffix);

        if ($reRender == true) {
            $this->value    =   $this->layout->renderById($this->id, '');
        }

        if ($useYearSuffixVariable == false) {
            $this->value    =   preg_replace('/([0-9]{2,4})/', '$1' . $suffix, $this->value);
            $this->value    =   str_replace('&#38' . $suffix . ';', '&#38;', $this->value);
        }

        return true;
    }

    /**
     * Adds an year suffix to the first cite, if et-al-subsequent-min or et-al-subsequent-use-first is used.
     *
     * @see disambiguate_BasedOnEtAlSubsequent.txt
     */
    private function addYearSuffixToSubsequentEtAl()
    {
        $ambiguous   =   $this->layout->renderById($this->id, '');
        Container::getRendered()->addAmbiguous($this->id, $ambiguous);
        if (Container::getRendered()->isAmbiguous($ambiguous, $this->id) == true) {
            // find identical cite
            $this->other    =   Container::getRendered()->getOtherByValue($ambiguous, 'ambiguous', $this->id);

            // render without etAlSubsequentMin and etAlSubsequentUseFirst use
            Container::getContext()->setDisambiguationOptions(
                'Geissler\CSL\Names\Name',
                array(
                    'etAlSubsequentMin'         =>  0,
                    'etAlSubsequentUseFirst'    =>  '')
            );

            $this->addYearSuffix();
        }
    }

    private function updateOtherValue()
    {
        $id         =   $this->other['id'];
        $original   =   Container::getRendered()->getById($id);

        if (isset($this->other['disambiguation']) == true) {
            $target     =   $this->other['citation'];
            $replace    =   str_replace($this->other['ambiguous'], $this->other['disambiguation'], $target);
            Container::getRendered()->addCitation($id, $replace);
            Container::getRendered()->addReplace($target, $replace);
        } else {
            Container::getRendered()->addCitation($id, $this->other['citation']);
        }

        if (isset($original['citation']) == true
            && isset($this->other['citation']) == true) {
            Container::getRendered()->addReplace($original['citation'], $this->other['citation']);
        }

        if (isset($this->other['suffix']) == true) {
            Container::getRendered()->addSuffix($id, $this->other['suffix']);
        }

        if (isset($this->other['disambiguation']) == true) {
            Container::getRendered()->addDisambiguation($id, $this->other['disambiguation']);
        }
    }
}
