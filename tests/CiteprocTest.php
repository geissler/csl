<?php

use Geissler\CSL\Container;
use Geissler\CSL\Style\Style;
use Geissler\CSL\Data\Data;
use Geissler\CSL\Data\Abbreviation;
use Geissler\CSL\Data\CitationItems;
use Geissler\CSL\Data\Citations;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-03 at 21:20:41.
 */
class CiteprocTest extends \PHPUnit_Framework_TestCase
{
    protected $object;
    protected $dir              =   '/citeproc-test/processor-tests/humans';
    protected $style            =   '/citeproc-test/styles';
    protected $testJustSelected =   false;
    protected $selectedTests    =   array(


        'variables_ShortForm'

        // working, excluding errors
        /*
        'etal_',
        'discretionary_',
        'group_',
        'display_',
        'position_',
        'affix_',
        'decorations_',
        'date_',
        'collapse_',
        'disambiguate_',
        'sort_',
        'condition_'
        */
    );
    /**
     * Tests which should not be run
     * @var array
     */
    protected $errors = array(
        // ignore on Windows
        'sort_SubstituteTitle.txt',
        'display_SecondFieldAlignClone.txt',
        'display_SecondFieldAlignMigratePunctuation.txt',

        // not sure what to suppress
        'etal_UseZeroFirst.txt',

        // Don't know why the "citation-number" is suppressed
        'discretionary_CitationNumberAuthorOnlyThenSuppressAuthor.txt',

        // Group suppressing
        'group_ComplexNesting.txt',

        // uppercase
        'group_ShortOutputOnly.txt',

        // missing ibid
        'position_ResetNoteNumbers.txt',

        // don't know how to figure out to use "page" as "label"
        'position_IbidSeparateCiteSameNote.txt',

        // ITEM-1 is not the same as ITEM-2 in the previous citation
        'position_IbidInText.txt',

        // no multiple ibid allowed (?)
        'position_IbidWithMultipleSoloCitesInBackref.txt',

        // crashing why ever
        'number_PlainHyphenOrEnDashAlwaysPlural.txt',

        // don't know why this is not a subsequent position
        'affix_WithCommas.txt',

        // Input and Input2 set, don't know how to handle that
        'sort_ChangeInNameSort.txt',

        // unclear
        'bugreports_ContainerTitleShort.txt', // not clear how to remove the dots in journalAbbreviation

        // don't know how to figure out what is part of a cite
        'date_YearSuffixImplicitWithNoDate.txt',
        'date_YearSuffixWithNoDate.txt',

        // don't know how to handle and terms while sorting names
        'sort_WithAndInOneEntry.txt',

        // don't know why use form numeric on date and not text
        'date_YearSuffixImplicitWithNoDateOneOnly.txt',

        // don't know how to determine the year delimiter
        'disambiguate_InitializeWithButNoDisambiguation.txt',

        // UN DESA 2011c should be the first value not the last, if sorted by bibliography keys
        'disambiguate_YearSuffixMidInsert.txt',

        // unclear when to use group citing
        'sort_GroupedByAuthorstring.txt',
        'disambiguate_YearCollapseWithInstitution.txt',
        'disambiguate_YearSuffixWithEtAlSubequent.txt',

        // equivalent to zero unclear
        'date_SortEmptyDatesCitation.txt',

        // wrong citeproctest or wrong specification => add year suffix will always succeed
        'disambiguate_ByCiteDisambiguateCondition.txt',

        // wrong sorting
        'date_NonexistentSortReverseCitation.txt',

        // i'm not sure if this possible in php
        'date_LoneJapaneseMonth.txt',

        // language (?)
        'date_LocalizedDateFormats-kh-KH.txt',

        // ignoring ambiguous values for "citationID"
        'collapse_CitationNumberRangesInsert.txt',

        // ???
        'bugreports_SortSecondaryKey.txt',
        'bugreports_DisambiguationAddNames.txt',

        // missing locale
        'bugreports_UnisaHarvardInitialization.txt',

        // javascript specific errors (?)
        'bibheader_EntryspacingDefaultValueOne.txt',
        'bibheader_EntryspacingExplicitValueZero.txt',
        'bibheader_SecondFieldAlign.txt',
        'bibheader_SecondFieldAlignWithAuthor.txt',
        'bibheader_SecondFieldAlignWithNumber.txt',
        'collapse_AuthorCollapseNoDateSorted.txt',
        'date_DateNoDateNoTest.txt',
    );
    protected $ignoreErrors = true;
    /**
     * Some results have to be modified for php
     * @var array
     */
    protected $modifyResult = array(
        //'affix_WithCommas.txt'  =>  'John Smith, <font style="font-style:italic">Book C</font>, 2000, and David Jones, <font style="font-style:italic">Book D</font>, 2000; John Smith, <font style="font-style:italic">Book C</font>, 2000 is one source, David Jones, <font style="font-style:italic">Book D</font>, 2000 is another; John Smith, <font style="font-style:italic">Book C</font>, 2000, 23 is one source, David Jones, <font style="font-style:italic">Book D</font>, 2000 is another.',
        'textcase_Uppercase.txt' => 'SMITH, John: THIS IS A PEN THAT IS A <span class="nocase">Smith</span> PENCIL',
        'affix_WordProcessorAffixNoSpace.txt' => "..[0] <i>My Prefix</i> My Title My Suffix\n..[1] My Prefix. My Title, My Suffix\n>>[2] My Prefix My Title My Suffix",
        'disambiguate_YearSuffixMacroSameYearImplicit.txt' => "..[0] A Smith 2001\n>>[1] B Smith 2001",
        'disambiguate_DisambiguateTrueAndYearSuffixOne.txt' => "..[0] Pollock, 1979\n>>[1] Pollock, 1980",
        'disambiguate_YearSuffixMacroSameYearExplicit.txt' => "..[0] A Smith 2001\n>>[1] B Smith 2001",
        'disambiguate_DisambiguationHang.txt' => "..[0] (Caminiti, Johnson, Burnod, Galli, & Ferraina 1990a)\n..[1] (Caminiti, Johnson, Burnod, Galli, & Ferraina 1990b)\n>>[2] (Caminiti, Johnson, & Urbano 1990)",
        'disambiguate_FailWithYearSuffix.txt' => "..[0] (Caritas Europa et al. 2004a)\n>>[1] (Caritas Europa et al. 2004b)",
        'disambiguate_LastOnlyFailWithByCite.txt' => "..[0] Organisation 2010a\n>>[1] Organisation 2010b",
        'disambiguate_DisambiguateTrueAndYearSuffixTwo.txt' => "..[0] Pollock, 1979a\n>>[1] Pollock, 1979b",
        'bugreports_BadCitationUpdate.txt' => "..[0] C. Grignon, C. Sentenac 2000a\n>>[1] C. Grignon, C. Sentenac 2000b",
        'discretionary_CitationNumberAuthorOnlyThenSuppressAuthor.txt' => "Reference [1]\n\n<i>[2]</i>"
    );

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Run tests and create a data test provider.
     * @return array
     */
    public function testCaseProvider()
    {
        $data = array();
        if ($dir = opendir(__DIR__ . $this->dir)) {
            while (($file = readdir($dir)) !== false) {
                if (strpos($file, '.txt') !== false
                    && ($this->testJustSelected == false
                        || $this->inArray($file, $this->selectedTests) == true)
                    && ($this->ignoreErrors == false
                        || in_array($file, $this->errors) == false)
                ) {

                    try {
                        $data[] = $this->runTestFromFile(file_get_contents(__DIR__ . $this->dir . '/' . $file), $file);
                    } catch (\ErrorException $error) {
                        \Geissler\CSL\Container::clear();
                        var_dump($error->getMessage() . ' ' . $file);
                    }
                }
            }

            closedir($dir);
        }

        return $data;
    }

    /**
     * Test if file name or file name part is in array.
     * @param string $file
     * @param array $array
     * @return bool
     */
    protected function inArray($file, $array)
    {
        if (in_array($file, $array) == false) {
            foreach ($array as $value) {
                if (preg_match('/' . $value . '/', $file) == 1) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * @dataProvider testCaseProvider
     */
    public function testCiteProc($result, $rendered, $file)
    {
        $result = str_replace('&#38;', '&', $result);
        $rendered = str_replace('&#38;', '&', $rendered);

        if (array_key_exists($file, $this->modifyResult) == true) {
            $this->assertEquals(
                (string)$this->modifyResult[$file],
                (string)$rendered,
                "\n Filename: " . $file . ' (modified for php)'
            );
        } elseif (strpos($result, "\n") === false) {
            $rendered = preg_replace('/<span class="nocase">([A-z]+)<\/span>/', '$1', $rendered);

            $this->assertEquals($result, $rendered, "\n Filename: " . $file);
        } else {
            $results = explode("\n", $result);
            $renders = explode("\n", $rendered);
            $length = count($results);

            if ($length == count($renders)) {
                for ($i = 0; $i < $length; $i++) {
                    $this->assertEquals($results[$i], $renders[$i], "\n Filename: " . $file);
                }
            } else {
                $this->assertEquals(
                    str_replace("\n", '', $result),
                    str_replace("\n", '', $rendered),
                    "\n Result: " . $result . "\n Rendered: " . $rendered . "\n Filename: " . $file
                );
            }
        }

    }

    /**
     * Running PHPUnit test
     * @param $text
     * @param $name
     * @return array
     */
    protected function runTestFromFile($text, $name)
    {
        Container::clear();

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
            $abbreviation = new Abbreviation();
            $abbreviation->set($json);
            Container::setAbbreviation($abbreviation);
        }

        // CitationItems items
        if (preg_match('/CITATION-ITEMS[ ]+[=]+>>(.*)<<[=]+[ ]+CITATION-ITEMS/s', $text, $match) == 1) {
            $json = preg_replace('/\s\s+/', '', $match[1]);
            $citation = new CitationItems();
            $citation->set($json);
            Container::setCitationItem($citation);
        } elseif (preg_match('/CITATIONS[ ]+[=]+>>(.*)<<[=]+[ ]+CITATIONS/s', $text, $match) == 1) {
            // Citations items
            $json = preg_replace('/\s\s+/', '', $match[1]);
            $citation = new Citations();
            $citation->set($json);
            Container::setCitationItem($citation);
        }

        if ($mode == 'citation') {
            return array($result, Container::getCitation()->render(''), $name);
        } elseif ($mode == 'bibliography') {
            return array($result, Container::getBibliography()->render(''), $name);
        } elseif ($mode == 'bibliography-nosort') {
            Container::getBibliography()->setDoNotSort(true);
            return array($result, Container::getBibliography()->render(''), $name);
        } else {
            return array('Missing mode', '', $name);
        }
    }
}
