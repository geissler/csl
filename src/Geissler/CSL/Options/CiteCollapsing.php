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
        preg_match('/([0-9]+)/', $data[0]['value'], $match);
        $last       =   $match[1];
        $position   =   0;
        $remove     =   array();

        for ($i = 1; $i < $length; $i++) {
            preg_match('/([0-9]+)/', $data[$i]['value'], $match);
            $actual =   $match[1];

            if ($last + 1 == $actual) {
                $remove[]   =   $i;
                $last++;
            } else {
                if ($position !== $i -1) {
                    $data[$position]['value']       .=  '–' . $data[$i - 1]['value'];
                    $data[$position]['delimiter']   =   $this->afterCollapseDelimiter;
                }
                $last       =   $actual;
                $position   =   $i;
            }
        }

        if ($position < $length - 1) {
            $data[$position]['value']       .=  '–' . $data[$length - 1]['value'];
            $data[$position]['delimiter']    =   '';
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
        $missingYear    =   array();

        // determine first value
        $actual = '';
        if (preg_match('/^(.*)([0-9]{4})([a-z]{0,2})$/', $data[0]['value'], $match) == 1) {
            $actual =   preg_quote($match[1], '/');
        } else {
            $missingYear[]  =   0;
        }

        $length         =   count($data);
        $lastCollapsed  =   0;
        for ($i = 1; $i < $length; $i++) {
            if ($actual !== ''
                && preg_match('/^' . $actual . '([0-9]{4})([a-z]{0,2})$/', $data[$i]['value'], $match) == 1) {
                $data[$i]['value']  =   preg_replace('/^' . $actual . '/', '', $data[$i]['value']);
                $lastCollapsed      =   $i;
            } elseif (preg_match('/^(.*)([0-9]{4})([a-z]{0,2})$/', $data[$i]['value'], $match) == 1) {
                $actual     =   preg_quote($match[1], '/');
                // use after collapse delimiter
                $data[$lastCollapsed]['delimiter']  =   $this->afterCollapseDelimiter;
            } else {
                $missingYear[]  =   $i;
            }
        }

        if (count($missingYear) > 0) {
            // collapse authors where year is missing and add delimiter
            $delimiter  =   Container::getContext()->get('delimiter', 'layout');
            foreach ($missingYear as $position) {
                if (isset($data[$position + 1]) == true
                    && isset($data[$position + 1]['value']) == true) {
                    if (preg_match('/^' . $data[$position]['value'] . '/', $data[$position + 1]['value']) == 1) {
                        $data[$position + 1]['value']   =   str_replace(
                            $data[$position]['value'],
                            $data[$position]['value'] . $delimiter,
                            $data[$position + 1]['value']
                        );
                        unset($data[$position]);
                    }
                }
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
        $position   =   -1;

        for ($i = 0; $i < $length; $i++) {
            if (preg_match('/([0-9]{4})([a-z]){0,2}$/', $data[$i]['value'], $match) == 1) {
                if ($position > -1) {
                    // a new year group has started
                    $data[$position]['value']       .=  '–';
                    $data[$position]['delimiter']   =   '';

                    // update previous delimiter
                    $data[$i - 1]['delimiter']  =   $this->afterCollapseDelimiter;

                    // remove
                    for ($j = $position + 1; $j < $i - 1; $j++) {
                        $remove[]   =   $j;
                    }
                }

                if (isset($match[2]) == true) {
                    // full year with suffix
                    $lastYearSuffix =   $match[2];
                    $position       =   $i;
                } else {
                    unset($lastYearSuffix);
                    $position   =   -1;
                }
            } elseif (isset($lastYearSuffix) == true) {
                $actualSuffix   =   $data[$i]['value'];
                $lastYearSuffix++;

                if (strcmp($lastYearSuffix, $actualSuffix) != 0) {
                    // not corresponding suffix
                    if ($position < $i - 1) {
                        // suffix range
                        $data[$position]['value']       .=  '–';
                        $data[$position]['delimiter']   =   '';

                        // remove
                        for ($j = $position + 1; $j < $i - $position; $j++) {
                            $remove[]   =   $j;
                        }
                    }

                    $position   =   $i;
                }

                $lastYearSuffix =   $data[$i]['value'];
            }
        }

        // remove unnecessary suffixes
        foreach ($remove as $i) {
            unset($data[$i]);
        }

        return array_values($data);
    }
}
