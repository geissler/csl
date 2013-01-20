<?php
namespace Geissler\CSL\Options\Disambiguation;

use Geissler\CSL\Options\Disambiguation\AddNames;
use Geissler\CSL\Options\Disambiguation\AddYearSuffix;
use Geissler\CSL\Options\Disambiguation\AddGivenName;
use Geissler\CSL\Options\Disambiguation\AddHiddenGivenName;
use Geissler\CSL\Options\Disambiguation\ChooseDisambiguate;
use Geissler\CSL\Options\Disambiguation\Store;
use Geissler\CSL\Container;

/**
 * Chain.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @license MIT
 */
class Chain
{
    /**
     * Create a chain of objects to disambiguate the ambiguous values. The first element tries to disambiguate the
     * values. If it fails, the ambiguous values are passed to the next chain element. The chain stops, if all
     * ambiguous values are disambiguated or if no succeeding chain element exists.
     *
     * @param array $ambiguous
     */
    public function __construct(array $ambiguous)
    {
        $store  =   new Store();
        $store->setAmbiguous($ambiguous);
        $chain  =   false;

        // last step
        if (Container::getContext()->isChooseDisambiguationActive() == true) {
            Container::getContext()->setChooseDisambiguateValue(false);
            $chain  =   new ChooseDisambiguate();
            $chain->setStore($store);
        }

        // step 3
        if (Container::getContext()->getValue('disambiguateAddYearSuffix', 'citation') == true) {
            $addYear    =   new AddYearSuffix();
            $addYear->setStore($store);
            $chain  =   $addYear;
        }

        // step 2, a
        // if disambiguate-add-names is set to "true", then the names still hidden as a result of
        // et-al abbreviation after the disambiguation attempt of disambiguate-add-names are
        // added one by one to all members of a set of ambiguous cites
        if (Container::getContext()->getValue('disambiguateAddNames', 'citation') === true
            && Container::getContext()->getValue('disambiguateAddGivenname', 'citation') == true) {
            Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
            $addHidden  =   new AddHiddenGivenName();
            $addHidden->setStore($store);

            if ($chain !== false) {
                $addHidden->setSuccessor($chain);
            }

            $chain  =   $addHidden;
        }

        // step 2
        if (Container::getContext()->getValue('disambiguateAddGivenname', 'citation') == true) {
            $addGiveName    =   new AddGivenName();
            $addGiveName->setStore($store);

            if ($chain !== false) {
                $addGiveName->setSuccessor($chain);
            }
            $chain  =   $addGiveName;
        }

        // step 1
        if (Container::getContext()->getValue('disambiguateAddNames', 'citation') === true) {
            $addNames   =   new AddNames();
            $addNames->setStore($store);

            if ($chain !== false) {
                $addNames->setSuccessor($chain);
            }
            $chain  =   $addNames;
        }

        if ($chain !== false) {
            $chain->setAmbiguous($ambiguous)
                ->setDisambiguate(array())
                ->disambiguate();
        }
    }
}
