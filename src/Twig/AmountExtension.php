<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;


class AmountExtension extends AbstractExtension {

    public function getFilters()
    {
        return [
            new TwigFilter('amount', [$this, 'amount']) // quand on utilise le filtre amount, on utilise la fonction amount qui se trouve dans cete objet là
        ];
    }

    // on met en place des valeurs par défault, que l'on peut changer dans Twig si on le souhaite
    public function amount($value, string $symbol = '€', string $decsep =',', string $thousandsep = ' ') {

        // 19229 => 192.29 €
        $finalValue = $value / 100;
        $finalValue = number_format($finalValue, 2, $decsep, $thousandsep);
        
        return $finalValue . ' ' . $symbol;

    }

}