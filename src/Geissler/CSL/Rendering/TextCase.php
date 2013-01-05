<?php
namespace Geissler\CSL\Rendering;

use Geissler\CSL\Interfaces\Renderable;
use Geissler\CSL\Interfaces\Modifiable;
use Geissler\CSL\Container;

/**
 * Renders the text case.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class TextCase implements Renderable, Modifiable
{
    /** @var boolean **/
    private $textCase;

    /**
     * Parses the quotes configuration.
     *
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->textCase    =   '';
        $this->modify($xml);
    }

    /**
     * Modify the configuration.
     *
     * @param \SimpleXMLElement $xml
     * @return \Geissler\CSL\Rendering\TextCase
     */
    public function modify(\SimpleXMLElement $xml)
    {
        foreach ($xml->attributes() as $name => $value) {
            if ($name == 'text-case') {
                $this->textCase    =   (string) $value;
            }
        }

        return $this;
    }

    /**
     * Apply the text case.
     *
     * @param string $data
     * @return string
     */
    public function render($data)
    {
        switch ($this->textCase) {
            case 'lowercase':
                return $this->keepNoCaseSpan(mb_strtolower($data), $data);
                break;
            case 'uppercase':
                return $this->keepNoCaseSpan(mb_strtoupper($data), $data);
                break;
            case 'capitalize-all':
                return $this->keepNoCaseSpan(mb_convert_case($data, \MB_CASE_TITLE), $data);
                break;
            case 'capitalize-first':
                return $this->capitalizeFirst($data);
                break;
            case 'sentence':
                return $this->renderSentence($data);
                break;
            case 'title':
                return $this->keepNoCaseSpan($this->renderTitle($data), $data);
                break;
            default:
                return $data;
                break;
        }
    }

    /**
     * Keep <span class="noncase"> elements untouched.
     *
     * @param string $render
     * @param string $original
     * @return string
     */
    private function keepNoCaseSpan($render, $original)
    {
        if (preg_match('/(<span class=(.){1,2}nocase(.){1,2}>[A-z]+<\/span>)/i', $original, $matches) == 1) {
            return preg_replace('/(<span class=(.){1,2}nocase(.){1,2}>[A-z]+<\/span>)/i', $matches[0], $render);
        }

        return $render;
    }

    /**
     * Capitalizes the first character of the first word, if the word is lowercase.
     *
     * @param string $data
     * @return string
     */
    private function capitalizeFirst($data)
    {
        return $this->keepNoCaseSpan(mb_strtoupper(mb_substr($data, 0, 1)) . mb_substr($data, 1), $data);
    }

    /**
     * Render sentence cases.
     *
     * @param string $data
     * @return string
     */
    private function renderSentence($data)
    {
        if (preg_match('/^[A-Z| |\.|,]+$/', $data) == 1) {
            return $this->keepNoCaseSpan(mb_substr($data, 0, 1) . mb_strtolower(mb_substr($data, 1)), $data);
        }

        return $this->capitalizeFirst($data);
    }

    /**
     * Render title cases.
     *
     * @param string $data
     * @return string
     */
    private function renderTitle($data)
    {
        if (Container::getLocale()->getLanguage() == 'en'
            && (Container::getData()->getVariable('language') == null)
                || Container::getData()->getVariable('language') == 'en') {
            // In both cases, stop words are lowercased, unless they are the first or last word in the string,
            // or follow a colon.
            $stopWords  =   array(
                                'a', 'an', 'and', 'as', 'at', 'but', 'by', 'down', 'for', 'from', 'in', 'into',
                                'nor', 'of', 'on', 'onto', 'or', 'over', 'so', 'the', 'till', 'to', 'up', 'via',
                                'with', 'yet');
            $values     =   explode(' ', $data);
            $length     =   count($values);

            if (preg_match('/^[A-Z| |\.|,]+$/', $data) == 1) {
                // For uppercase strings, the first character of each word remains capitalized.
                // All other letters are lowercase.
                for ($i = 0; $i < $length; $i++) {
                    if ($i > 0
                        && $i < $length - 1
                        && in_array(mb_strtolower($values[$i]), $stopWords) == true) {
                            $values[$i] = mb_strtolower($values[$i]);
                    } else {
                        $values[$i] =   mb_substr($values[$i], 0, 1) . mb_strtolower(mb_substr($values[$i], 1));
                    }
                }
            } else {
                // For lower or mixed case strings, the first character of each lowercase word is capitalized.
                // The case of words in mixed or uppercase stays the same.
                for ($i = 0; $i < $length; $i++) {
                    if ($i > 0
                        && $i < $length - 1
                        && in_array(mb_strtolower($values[$i]), $stopWords) == true) {
                            $values[$i] = mb_strtolower($values[$i]);
                    } else {
                        $values[$i] =   mb_strtoupper(mb_substr($values[$i], 0, 1)) . mb_substr($values[$i], 1);
                    }
                }
            }

            return implode(' ', $values);
        }

        return $data;
    }
}
