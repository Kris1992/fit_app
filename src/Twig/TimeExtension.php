<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TimeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('time', [$this, 'transformToTimeFormat'], ['is_safe' => ['html']]),
        ];
    }

    public function transformToTimeFormat(int $seconds): string
    {
        $array['hour'] = (int)($seconds / (60 * 60));
        $array['minute'] = (int)(($seconds / 60) % 60);
        $array['second'] = (int)($seconds % 60);

        foreach ($array as $key => $value) {
            $array[$key] = $this->transformToTimeString($value);
        }

        $format = '%s:%s:%s';
        $timeString = sprintf($format, $array['hour'], $array['minute'], $array['second']);

        return $timeString;
    }

    /**
     * transformToTimeString Transform time value(hour, minute etc.) to string format 01,11 etc.
     * @param  int    $value Time value to transform 
     * @return string
     */
    private function transformToTimeString(int $value): string
    {
        $value = str_pad((string)$value, 2, '0', STR_PAD_LEFT);

        return $value;
    }
}
