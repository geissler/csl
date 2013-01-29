<?php
namespace Geissler\CSL\Options;

use Geissler\CSL\Interfaces\Optional;
use Geissler\CSL\Container;

/**
 * Cite groups (author and author-date styles), and numeric cite ranges (numeric styles) can be collapsed
 * through the use of the collapse attribute.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class CiteCollapsing implements Optional
{
    /** @var string */
    private $yearSuffixDelimiter;
    /** @var string */
    private $afterCollapseDelimiter;
    /** @var string */
    private $collapse;

    /**
     * Specifies the cite delimiter to be used after a collapsed cite group.
     *
     * @param string $afterCollapseDelimiter
     * @return \Geissler\CSL\Options\CiteCollapsing
     */
    public function setAfterCollapseDelimiter($afterCollapseDelimiter)
    {
        $this->afterCollapseDelimiter = $afterCollapseDelimiter;
        return $this;
    }

    /**
     * Activates cite grouping and collapsing.
     *
     * @param string $collapse
     * @return \Geissler\CSL\Options\CiteCollapsing
     */
    public function setCollapse($collapse)
    {
        $this->collapse = $collapse;
        return $this;
    }

    /**
     * Specifies the delimiter for year-suffixes.
     *
     * @param string $yearSuffixDelimiter
     * @return \Geissler\CSL\Options\CiteCollapsing
     */
    public function setYearSuffixDelimiter($yearSuffixDelimiter)
    {
        $this->yearSuffixDelimiter = $yearSuffixDelimiter;
        return $this;
    }

    /**
     * Collapses the cites.
     *
     * @param array $data
     * @return array
     */
    public function apply(array $data)
    {
        if (isset($this->collapse) == false) {
            return $data;
        }

        // use layout delimiter if no other is set
        $delimiter  =   Container::getContext()->get('delimiter', 'layout');
        if (isset($this->afterCollapseDelimiter) == false) {
            $this->afterCollapseDelimiter   =   $delimiter;
        }

        if (isset($this->yearSuffixDelimiter) == false) {
            $this->yearSuffixDelimiter  =   $delimiter;
        }

        // citation-items
        if (isset($data[0][0]) == true) {
            $length =   count($data);

            for ($i = 0; $i < $length; $i++) {
                switch ($this->collapse) {
                    case 'citation-number':
                        $data[$i]   =   $this->citationNumber($data[$i]);
                        break;
                    case 'year':
                        $data[$i]   =   $this->year($data[$i]);
                        break;
                    case 'year-suffix':
                        $data[$i]   =   $this->yearSuffix($data[$i]);
                        break;
                    case 'year-suffix-ranged':
                        $data[$i]   =   $this->yearSuffixRanged($data[$i]);
                        break;
                }
            }
        } else {
            switch ($this->collapse) {
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
        }

        return $data;
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
        $last       =   $data[0]['value'];
        $position   =   0;
        $remove     =   array();

        for ($i = 1; $i < $length; $i++) {
            if ($last + 1 == $data[$i]['value']) {
                $remove[]   =   $i;
                $last++;
            } else {
                if ($position !== $i -1) {
                    $data[$position]['value']       .=  '-' . $data[$i - 1]['value'];
                    $data[$position]['position']    =   $this->afterCollapseDelimiter;
                }
                $last       =   $data[$i]['value'];
                $position   =   $i;
            }
        }

        if ($position < $length - 1) {
            $data[$position]['value']    .=  '-' . $last;
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
        // determine first value
        preg_match('/^(.*)([0-9]{4})([a-z]{0,2})$/', $data[0]['value'], $match);
        $actual =   preg_quote($match[1], '/');

        $length =   count($data);
        for ($i = 1; $i < $length; $i++) {
            if (preg_match('/^' . $actual . '([0-9]{4})([a-z]{0,2})$/', $data[$i]['value'], $match) == 1) {
                $data[$i]['value']  =   str_replace($actual, '', $data[$i]['value']);
            } elseif (preg_match('/^(.*)([0-9]{4})([a-z]{0,2})$/', $data[$i]['value'], $match) == 1) {
                $actual     =   preg_quote($match[1], '/');
            }
        }

        return array_values($data);
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
            if (preg_match('/^([0-9]{4})([a-z]{1,2})$/', $data[$i]['value'], $match) == 1) {
                $data[$i]['value']          =   $match[2];
                $data[$i - 1]['delimiter']  =   $this->yearSuffixDelimiter;
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
            if (preg_match('/^(' . $this->yearSuffixDelimiter . '){0,1}([a-z]){1,2}/', $data[$i], $match) == 1) {
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
                preg_match('/^[' . $this->yearSuffixDelimiter . ']{0,1}([a-z]){1,2}/', $data[$position], $match);
                if (in_array($position, $remove) == false) {
                    if (isset($match[1]) == false
                        || $last !== $match[1]) {
                        $data[$position]    =   $this->addSuffixRange($data[$position], $last);
                    }

                    $data[$position]    .=  $this->afterCollapseDelimiter;
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
        $return =   str_replace(
            array(
                $this->yearSuffixDelimiter . $delimiter,
                $delimiter. $this->yearSuffixDelimiter,
                $delimiter . $delimiter,
                $this->yearSuffixDelimiter . $this->yearSuffixDelimiter,
                $this->afterCollapseDelimiter . $this->afterCollapseDelimiter,
                $delimiter . $this->afterCollapseDelimiter,
                $this->afterCollapseDelimiter . $delimiter,
            ),
            array(
                $this->yearSuffixDelimiter,
                $this->yearSuffixDelimiter,
                $delimiter,
                $this->yearSuffixDelimiter,
                $this->afterCollapseDelimiter,
                $this->afterCollapseDelimiter,
                $this->afterCollapseDelimiter,
            ),
            $return
        );

        $return =   preg_replace('/([,|;]) ([,|;])/', '$1', $return);
        $return =   preg_replace('/[,][,]+/', ',', $return);
        $return =   preg_replace('/[;][;]+/', ';', $return);
        $return =   preg_replace('/[ ][ ]+/', ' ', $return);
        return preg_replace('/([,|;| ]+)$/', '', $return);
    }
}
