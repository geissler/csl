This project is still in an early alpha state, nearly the half of the tests is failing. The goal is to implement a
parser which is 100% compatible with the [CSL 1.0.1](http://citationstyles.org/downloads/specification.html "CSL")
standard, is 100% OOP and follows the
[PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md "PSR-2") coding
standard.

## Installation
### Via [composer](http://getcomposer.org/ "composer")
Add to the `composer.json` the `require` key and run composer install.
```
    "require" : {
        "geissler/csl": "dev-master"
    }
```
### Other
Make sure you are using a
[PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md "PSR-2")
compatible autoloader.

## Usage
Render a single bibliography entry.
```php
    // include the composer autoloader
    require 'vendor/autoload.php';

    use Geissler\CSL\CSL;

    // the name of the style you want to use
    $style = 'american-journal-of-archaeology';

    // the data do be displayed as a json array
    $input = '[
          {
              "id": "ITEM-1",
              "author" : [
                 {
                    "family": "Wallace-Hadrill",
                    "given": "Andrew"
                 }
              ],
              "issued": {
                  "date-parts": [
                      [
                          "2011"
                      ]
                  ]
              },
              "title": "The monumental centre of Herculaneum. In search of the identities of the public buildings",
              "container-title" : "Journal of Roman Archaeology",
              "volume" : "24",
              "page" : "121-160",
              "original-publisher-place" : "Ann Arbor, Mich.",
              "type": "article-journal"
          }
    ]';

    $csl = new CSL();
    echo $csl->bibliography($style, $input);

    // this will output the following
    Wallace-Hadrill, Andrew. 2011. "The monumental centre of Herculaneum. In search of the identities of the public
    buildings". Journal of Roman archaeology 24: 121-160.
```
As HTML
```html
    <div class="csl-bib-body">
        <div class="csl-entry">Wallace-Hadrill, Andrew. 2011. "The monumental centre of Herculaneum. In search of the
        identities of the public buildings". <font style="font-style:italic">Journal of Roman archaeology</font>
        24: 121-160.</div>
    </div>
```

## Configuration

## Tests and Comments
[![Build Status](https://travis-ci.org/geissler/csl.png)](https://travis-ci.org/geissler/csl)

Most of the examples for the phpunit-tests under *tests/src*, all files in *tests/citeproc-test* and some comments are
taken/copied from [CiteProc Test](https://bitbucket.org/bdarcus/citeproc-test "CiteProc Test"), **"the standard test
bundle for use in CSL processor and style development"** written by Frank G. Bennett, Jr. and Bruce D'Arcus.