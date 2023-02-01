<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class WeightExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('weight', [$this, 'getWeightFormat'])
        ];
    }

    /**
     * @param string $weight
     * @return string
     */
    public function getWeightFormat(string $weightString): string
    {
        if (is_numeric($weightString) === false) return $weightString;
        $resultWeight = '';
        $weight = intval($weightString);
        if ($weight > 1000) {
            $weightKg = intval($weight / 1000);
            $weight -= $weightKg * 1000;
            $resultWeight .= sprintf('%skg', $weightKg);
        }
        if ($weight > 0) {
            $resultWeight .= sprintf(' %sg', $weight);
        }
        return trim($resultWeight);
    }

}