<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Класс для привидения веса из граммов в кг и г
 */
class WeightExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('weight', [$this, 'getWeightFormat']),
        ];
    }

    public function getWeightFormat(string $weightString): string
    {
        if (false === is_numeric($weightString)) {
            return $weightString;
        }
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
