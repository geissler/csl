<?php

use Geissler\CSL\Container;
use Geissler\CSL\Style\Style;
use Geissler\CSL\Data\Data;
use Geissler\CSL\Data\Abbreviation;
use Geissler\CSL\Data\Citation;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-03 at 21:20:41.
 */
class CiteprocTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $object;
    protected $dir = '/processor-tests/humans';
    protected $style = '/styles';
    protected $testJustSelected = false;
    protected $selectedTests = array('affix_WithCommas.txt');
    protected $errors = array('bugreports_UnisaHarvardInitialization.txt');
    protected $ignoreErrors = true;
    protected $modifyResult = array(
        'textcase_TitleCapitalization.txt' => 'This IS a Pen That Is a <span class="nocase">smith</span> Pencil',
        'affix_WithCommas.txt'  =>  'John Smith, <font style="font-style:italic">Book C</font>, 2000, and David Jones, <font style="font-style:italic">Book D</font>, 2000; John Smith, <font style="font-style:italic">Book C</font>, 2000 is one source, David Jones, <font style="font-style:italic">Book D</font>, 2000 is another; John Smith, <font style="font-style:italic">Book C</font>, 2000, 23 is one source, David Jones, <font style="font-style:italic">Book D</font>, 2000 is another.');

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testCaseProvider()
    {
        $data   =   array();
        if ($dir = opendir(__DIR__ . $this->dir)) {
            while (($file = readdir($dir)) !== false) {
                if (strpos($file, '.txt') !== false
                    && ($this->testJustSelected == false
                        || in_array($file, $this->selectedTests) == true)
                    && ($this->ignoreErrors == false
                        || in_array($file, $this->errors) == false)) {
                    $data[] = $this->runTestFromFile(file_get_contents(__DIR__ . $this->dir . '/' . $file), $file);
                }
            }

            closedir($dir);
        }

        return $data;
    }

    /**
     * @dataProvider testCaseProvider
     */
    public function testCiteProc($result, $renderd, $file)
    {
        // use font-style instead of i etc.
        $result = str_replace('<i>', '<font style="font-style:italic">', $result);
        $result = str_replace('</i>', '</font>', $result);

        if (array_key_exists($file, $this->modifyResult) == true) {
            $this->assertEquals($this->modifyResult[$file], $renderd, "\n Filename: " . $file . ' (modified for php)');
        } elseif (strpos($result, "\n") === false) {
            $this->assertEquals($result, $renderd, "\n Filename: " . $file);
        } else {
            $results    = explode("\n", $result);
            $renders    = explode("\n", $renderd);
            $length     =   count($results);

            for ($i = 0; $i < $length; $i++) {
                $this->assertEquals($results[$i], $renders[$i], "\n Filename: " . $file);
            }
        }

    }

    protected function runTestFromFile($text, $name)
    {
        preg_match('/MODE [=]+>>(.*)<<[=]+ MODE/s', $text, $match);
        $mode = preg_replace('/\s\s+/', '', $match[1]);
        $mode = preg_replace('/\n/', '', $mode);

        preg_match('/RESULT [=]+>>(.*)<<[=]+ RESULT/s', $text, $match);
        $result = preg_replace('/\s\s+/', '', $match[1]);
        $result = preg_replace('/^\n/', '', $result);
        $result = preg_replace('/\n$/', '', $result);

        preg_match('/CSL [=]+>>(.*)<<[=]+ CSL/s', $text, $match);
        $csl = preg_replace('/^\n/', '', $match[1]);

        preg_match('/INPUT [=]+>>(.*)<<[=]+ INPUT/s', $text, $match);
        $json = preg_replace('/\s\s+/', '', $match[1]);

        $style = new Style();
        if (strpos($csl, '<') !== false) {
            $style->readXml(new \SimpleXMLElement($csl));
        } else {
            $csl = preg_replace('/\n/', '', $csl);
            $csl = str_replace('.csl', '', $csl);
            $style->setDir(__DIR__ . $this->style)
                ->readFile($csl);
        }
        $data = new Data();
        $data->set($json);
        Container::setData($data);
        Container::getContext()->setName($mode);

        // Abbreviations
        if (preg_match('/ABBREVIATIONS [=]+>>(.*)<<[=]+ ABBREVIATIONS/s', $text, $match) == 1) {
            $json = preg_replace('/\s\s+/', '', $match[1]);
            $abbreviation    =   new Abbreviation();
            $abbreviation->set($json);
            Container::setAbbreviation($abbreviation);
        }

        // Citation items
        if (preg_match('/CITATION-ITEMS [=]+>>(.*)<<[=]+ CITATION-ITEMS/s', $text, $match) == 1) {
            $json = preg_replace('/\s\s+/', '', $match[1]);
            $citation   =   new Citation();
            $citation->set($json);
            Container::setCitationItem($citation);
        }

        if ($mode == 'citation') {
            return array($result, Container::getCitation()->render(''), $name);

        } elseif ($mode == 'bibliography') {
            return array($result, Container::getBibliography()->render(''), $name);
        } else {
            return array('Missing mode', '', $name);
        }
    }
}
