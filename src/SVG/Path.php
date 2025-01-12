<?php

namespace Maantje\Charts\SVG;

use Stringable;
use Maantje\Charts\Svgable;

readonly class Path implements Stringable {
    use Svgable;

    private string $d;

    /**
     * @param  array<int, array{float, float}>  $points
     */
    public function __construct(
        private array $points = [],
        private string $fill = 'none',
        private string $stroke = 'black',
        private float $strokeWidth = 1,
        private float $smoothing = 0.2,
        private float $flattening = 0.85,
        private ?string $transform = null
    ) {
        $this->d = $this->svgPath($this->points, 'bezier');
        // ...
    }

    protected function convertPointsToPath($points): string {
        $index = 0;

        return array_reduce($points, function ($carry, $point) use (&$index, $points) {
            $carry .= ($index === 0
                ? sprintf('M %s, %s ', $point[0], $point[1])
                : $this->line($points, $index)
            );

            $index++;

            return $carry;
        }, '');
    }

    protected function bezier($point, $i, $a) {
        [$cpsX, $cpsY] = $this->controlPoint($a[$i - 1], ($a[$i - 2] ?? null), $point);
        [$cpeX, $cpeY] = $this->controlPoint($point, $a[$i - 1], ($a[$i + 1] ?? null), true);

        return "C {$cpsX},{$cpsY} {$cpeX},{$cpeY} {$point[0]},{$point[1]}";
    }

    protected function controlPoint($current, $previous = null, $next = null, $reverse = false): array {
        $p = $previous ?? $current;
        $n = $next ?? $current;

        $o = $this->line($p, $n);

        $flat = ((cos($o['angle']) * $this->flattening) - 0) * (0 - 1) / (1 - 0) + 1;
        $angle = $o['angle'] * $flat + ($reverse ? pi() : 0);
        $length = $o['length'] * $this->smoothing;

        $x = $current[0] + cos($angle) * $length;
        $y = $current[1] + sin($angle) * $length;

        return [$x, $y];
    }

    protected function line($pointA, $pointB): array {
        $lengthX = $pointB[0] - $pointA[0];
        $lengthY = $pointB[1] - $pointA[1];

        return [
            'length' => sqrt(pow($lengthX, 2) + pow($lengthY, 2)),
            'angle' => atan2($lengthY, $lengthX),
        ];
    }

    protected function svgPath($points, $command, $idx = 0): string {
        return array_reduce($points, function($carry, $point) use (&$idx, $points, $command) {
            $carry .= ($idx === 0
                ? sprintf('M %s, %s ', $point[0], $point[1])
                : $this->{$command}($point, $idx, $points)
            );

            $idx++;

            return $carry;
        }, '');
    }

    public function __toString(): string {
        $attributes = $this->toSvgProps();

        return sprintf('<path %s />', $attributes);
    }
}
