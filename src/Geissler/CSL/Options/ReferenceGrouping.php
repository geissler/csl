<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Interfaces\Optional;
use Geissler\CSL\Container;
use Geissler\CSL\Names\Names;

/**
 * Reference Grouping.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class ReferenceGrouping implements Optional
{
    /** @var string */
    private $value;
    /** @var string */
    private $rule;
    /** @var \Geissler\CSL\Names\Names */
    private $names;

    /**
     * Set the subsequent-author-substitute value.
     *
     * @param string $value
     * @return ReferenceGrouping
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set the subsequent-author-substitute-rule rule.
     *
     * @param string $rule
     * @return ReferenceGrouping
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
        return $this;
    }

    /**
     * Apply the reference grouping rule, if activated by setting a replace value.
     *
     * @param array $data
     * @return array
     */
    public function apply(array $data)
    {
        Container::getData()->moveToFirst();
        $this->names    =   Container::getContext()
            ->get('layout', 'layout')
            ->getChildElement('\Geissler\CSL\Names\Names');

        if (isset($this->value) == false
            || is_object($this->names) == false) {
            return $data;
        }

        switch ($this->rule) {
            case 'complete-all':
                return $this->completeAll($data);
                break;
            case 'complete-each':
                return $this->completeEach($data);
                break;
            case 'partial-each':
                return $this->partialEach($data);
                break;
            case 'partial-first':
                return $this->partialFirst($data);
                break;
            default:
                return $data;
                break;
        }
    }

    /**
     * When all rendered names of the name variable match those in the preceding bibliographic entry,
     * the value of subsequent-author-substitute replaces the entire name list.
     *
     * @param array $bibliography
     * @return array
     */
    private function completeAll($bibliography)
    {
        $previous   =   preg_quote($this->names->render(''), '/');
        $length     =   count($bibliography);

        for ($i = 1; $i < $length; $i++) {
            Container::getData()->next();
            if (preg_match('/^' . $previous . '/', $bibliography[$i]) == 1) {
                $bibliography[$i]   =   preg_replace('/^' . $previous . '/', $this->value, $bibliography[$i]);
            } else {
                $previous   =   preg_quote($this->names->render(''), '/');
            }
        }

        return $bibliography;
    }

    /**
     * Requires a complete match like "complete-all", but now the value of subsequent-author-substitute
     * substitutes for each rendered name.
     *
     * @param array $bibliography
     * @return array
     */
    private function completeEach($bibliography)
    {
        $previous   =   preg_quote($this->names->render(''), '/');
        $length     =   count($bibliography);

        for ($i = 1; $i < $length; $i++) {
            if (preg_match('/^' . $previous . '/', $bibliography[$i]) == 1) {
                $names  =   $this->names->renderAsArray('');
                $values =   array_fill(0, count($names), $this->value);
                $value  =   str_replace($names, $values, $previous);

                $bibliography[$i]   =   preg_replace('/^' . $previous . '/', $value, $bibliography[$i]);
                Container::getData()->next();
            } else {
                Container::getData()->next();
                $previous   =   preg_quote($this->names->render(''), '/');
            }
        }

        return $bibliography;
    }

    /**
     * When one or more rendered names in the name variable match those in the preceding bibliographic entry,
     * the value of subsequent-author-substitute substitutes for each matching name. Matching starts with the
     * first name, and continues up to the first mismatch.
     *
     * @param array $bibliography
     * @return array
     */
    private function partialEach($bibliography)
    {
        $previous   =   $this->names->renderAsArray('');
        $length     =   count($bibliography);

        for ($i = 1; $i < $length; $i++) {
            Container::getData()->next();
            $names  =   $this->names->renderAsArray('');
            foreach ($names as $name) {
                if (in_array($name, $previous) == true) {
                    $bibliography[$i]   =   preg_replace(
                        '/' . preg_quote($name, '/') . '/',
                        $this->value,
                        $bibliography[$i]
                    );
                } else {
                    break;
                }
            }

            $previous   =   $names;
        }

        return $bibliography;
    }

    /**
     * As "partial-each", but substitution is limited to the first name of the name variable.
     *
     * @param array $bibliography
     * @return array
     */
    private function partialFirst($bibliography)
    {
        $previous   =   $this->names->renderAsArray('');
        $length     =   count($bibliography);

        for ($i = 1; $i < $length; $i++) {
            Container::getData()->next();
            $names  =   $this->names->renderAsArray('');

            if (isset($names[0]) == true
                && in_array($names[0], $previous) == true) {
                $bibliography[$i]   =   preg_replace(
                    '/' . preg_quote($names[0], '/') . '/',
                    $this->value,
                    $bibliography[$i]
                );
            }

            $previous   =   $names;
        }

        return $bibliography;
    }
}
