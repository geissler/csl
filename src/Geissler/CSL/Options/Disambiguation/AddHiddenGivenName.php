<?php
namespace Geissler\CSL\Options\Disambiguation;

use Geissler\CSL\Interfaces\Disambiguate;
use Geissler\CSL\Options\Disambiguation\AddGivenName;
use Geissler\CSL\Container;

/**
 * AddHiddenGivenName.
 *
 * @author Benjamin Geißler <benjamin.geissler@gmail.com>
 * @licence MIT
 */
class AddHiddenGivenName extends AddGivenName implements Disambiguate
{
    /**
     * Try to disambiguate the ambiguous values. If not possible, pass the values to the successor and try to
     * disambiguate with the successor. If possible, store ambiguous and disambiguated values.
     */
    public function disambiguate()
    {
        $etAl           =   Container::getContext()->getValue('etAlUseFirst', 'citation');
        $maxNames       =   Container::getContext()->get('layout', 'layout')
            ->getChildElement('\Geissler\CSL\Names\Names')
            ->getMaxNumberOfNames();
        $disambiguated  =   false;

        $this->tmpDisambiguate  =   $this->getDisambiguate();
        $this->tmpAmbiguous     =   $this->getAmbiguous();

        do {
            Container::getContext()->removeDisambiguationOptions('Geissler\CSL\Names\Name');
            $etAl++;
            Container::getContext()->setDisambiguationOptions(
                'Geissler\CSL\Names\Name',
                array(
                    'etAlUseFirst' => $etAl
                )
            );

            $disambiguated  =   $this->addGivenName($this->tmpAmbiguous);
        } while ($disambiguated == false && $etAl <= $maxNames);

        if ($disambiguated == true) {
            $this->store($this->tmpDisambiguate, $this->tmpAmbiguous);
        } else {
            $this->succeed($this->tmpDisambiguate, $this->tmpAmbiguous);
        }
    }
}
