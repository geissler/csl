<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Container;

/**
 * Cite groups (author and author-date styles), and numeric cite ranges (numeric styles) can be collapsed
 * through the use of the collapse attribute.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class CiteCollapsing
{
    /** @var string */
    private $suffixDelimiter;
    /** @var string */
    private $collapseDelimiter;

    /**
     * Collapses the cites.
     *
     * @param array $data
     * @param string $delimiter
     * @return string
     */
    public function collapse(array $data, $delimiter)
    {
        if (Container::getContext()->getValue('yearSuffixDelimiter', 'citation') === '') {
            $this->suffixDelimiter  =   $delimiter;
        } else {
            $this->suffixDelimiter  =   Container::getContext()->getValue('yearSuffixDelimiter', 'citation');
        }

        if (Container::getContext()->getValue('afterCollapseDelimiter', 'citation') === '') {
            $this->collapseDelimiter    =   $delimiter;
        } else {
            $this->collapseDelimiter    =   Container::getContext()->getValue('afterCollapseDelimiter', 'citation');
        }

        switch (Container::getContext()->getValue('collapse', 'citation')) {
            case 'citation-number':
                $data   =   $this->citationNumber($data);
                break;
            case 'year':
                $data   =   $this->year($data);
                break;
            case 'year-suffix':
                $data   =   $this->yearSuffix($data);
                break;
            case 'year-suffix-ranged':
                $data   =  $this->yearSuffixRanged($data);
                break;
        }

        return $this->implode($data, $delimiter);
    }

    /**
     * Collapses ranges of cite numbers.
     *
     * @param array $data
     * @return array
     */
    private function citationNumber(array $data)
    {
        $length     =   count($data);
        $last       =   $data[0];
        $position   =   0;
        $remove     =   array();

        for ($i = 1; $i < $length; $i++) {
            if ($last + 1 == $data[$i]) {
                $remove[]   =   $i;
                $last++;
            } else {
                if ($position !== $i -1) {
                    $data[$position] .= '-' . $data[$i - 1] . $this->collapseDelimiter;
                }
                $last       =   $data[$i];
                $position   =   $i;
            }
        }

        if ($position < $length - 1) {
            $data[$position]    .=  '-' . $last;
        }

        foreach ($remove as $i) {
            unset($data[$i]);
        }

        return array_values($data);
    }

    /**
     * Collapses cite groups by suppressing the output of the cs:names element for subsequent cites in the group.
     *
     * @param array $data
     * @return array
     */
    private function year(array $data)
    {
        preg_match('/^(.*) ([0-9]{4})([a-z]{0,2})([,|;|\.| ]){0,2}$/', $data[0], $match);
        $length =   count($data);
        $actual =   false;
        if (isset($match[1]) == true) {
            $actual =   str_replace('/', '\/', $match[1]);
        }

        for ($i = 1; $i < $length; $i++) {
            if ($actual !== false
                && preg_match('/^' . $actual . '/', $data[$i], $match) == 1) {
                $data[$i]   =   trim(str_replace($actual, '', $data[$i]));
            } elseif (preg_match('/^(.*) ([0-9]{4})([a-z]{0,2})([,|;|\.| ]){0,2}$/', $data[$i], $match) == 1) {
                $actual =   str_replace('/', '\/', $match[1]);
            }
        }

        return $data;
    }

    /**
     * Collapses as "year", but also suppresses repeating years within the cite group.
     *
     * @param array $data
     * @return array
     */
    private function yearSuffix(array $data)
    {
        $data   =   $this->year($data);
        $length =   count($data);

        for ($i = 1; $i < $length; $i++) {
            if (preg_match('/^([0-9]{4})([a-z]{1,2})/', $data[$i], $match) == 1) {
                $data[$i] = $this->suffixDelimiter . $match[2];
            }
        }

        return $data;
    }

    /**
     * Collapses as "year-suffix", but also collapses ranges of year-suffixes.
     *
     * @param array $data
     * @return array
     */
    private function yearSuffixRanged(array $data)
    {
        $data       =   $this->yearSuffix($data);
        $length     =   count($data);
        $remove     =   array();
        $position   =   0;
        $last       =   false;

        for ($i = 1; $i < $length; $i++) {
            if (preg_match('/^(' . $this->suffixDelimiter . '){0,1}([a-z]){1,2}/', $data[$i], $match) == 1) {
                $actual =   $match[2];

                if ($last == false) {
                    // first collapsed entry
                    $last       =   $actual;

                    if (preg_match('/([0-9]{4})([a-z]{1,2})/', $data[$i - 1], $match) == 1
                        || preg_match('/^(([a-z]{1,2}))/', $data[$i - 1], $match) == 1) {
                        $first  =   $match[2];
                        $first++;

                        if (strcmp($first, $last) == 0) {
                            // collapsing starts with first entry
                            $position   =   $i - 1;
                            $remove[]   =   $i;
                        } else {
                            $position   =   $i;
                        }
                    } else {
                        $position   =   $i;
                    }

                } else {
                    $test   =   $last;
                    $test++;
                    if (strcmp($test, $actual) == 0) {
                        $remove[]   =   $i;
                        $last       =   $test;
                    } else {
                        $data[$position]    =   $this->addSuffixRange($data[$position], $last);
                        $position   =   $i;
                        $last   =   $actual;
                    }
                }
            } elseif ($last !== false) {
                // add to last if not identical
                preg_match('/^[' . $this->suffixDelimiter . ']{0,1}([a-z]){1,2}/', $data[$position], $match);
                if (in_array($position, $remove) == false) {
                    if (isset($match[1]) == false
                        || $last !== $match[1]) {
                        $data[$position]    =   $this->addSuffixRange($data[$position], $last);
                    }

                    $data[$position]    .=  $this->collapseDelimiter;
                }
                $last   =   false;
            }
        }

        if ($last !== false
            && $position !== $length - 1) {
            $data[$position]    =   $this->addSuffixRange($data[$position], $last);
            $remove[]   =   $length - 1;
        }

        foreach ($remove as $i) {
            unset($data[$i]);
        }

        return array_values($data);
    }

    /**
     * Add collapsed range to previous range.
     *
     * @param string $value
     * @param string $suffix
     * @return string
     */
    private function addSuffixRange($value, $suffix)
    {
        if (preg_match('/([0-9]{4})([a-z]{1,2})/', $value) == 1) {
            return preg_replace('/([0-9]{4})([a-z]{1,2})/', '$1$2–' . $suffix, $value);
        } else {
            return preg_replace('/([a-z]{1,2})/', '$1–' . $suffix, $value);
        }

    }

    /**
     * Collapses the entries and removes duplicated and wrong delimiters.
     *
     * @param array $data
     * @param string $delimiter
     * @return string
     */
    private function implode($data, $delimiter)
    {
        $return =   implode($delimiter, $data);
        $return =   str_replace($this->suffixDelimiter . $delimiter, $this->suffixDelimiter, $return);
        $return =   str_replace($delimiter. $this->suffixDelimiter, $this->suffixDelimiter, $return);
        $return =   str_replace($delimiter . $delimiter, $delimiter, $return);
        $return =   str_replace($this->suffixDelimiter . $this->suffixDelimiter, $this->suffixDelimiter, $return);
        $return =   str_replace($this->collapseDelimiter . $this->collapseDelimiter, $this->collapseDelimiter, $return);
        $return =   str_replace($delimiter . $this->collapseDelimiter, $this->collapseDelimiter, $return);
        $return =   str_replace($this->collapseDelimiter . $delimiter, $this->collapseDelimiter, $return);
        $return =   preg_replace('/([,|;]) ([,|;])/', '$1', $return);
        $return =   preg_replace('/[,][,]+/', ',', $return);
        $return =   preg_replace('/[;][;]+/', ';', $return);
        $return =   preg_replace('/[ ][ ]+/', ' ', $return);
        return preg_replace('/([,|;| ]+)$/', '', $return);
    }
}
