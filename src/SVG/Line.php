<?php

namespace Maantje\Charts\SVG;

use Stringable;
use Maantje\Charts\Svgable;

readonly class Line implements Stringable {
    use Svgable;

    public function __construct(
        private float $x1 = 0,
        private float $y1 = 0,
        private float $x2 = 100,
        private float $y2 = 100,
        private string $strokeDashArray = '',
        private string $stroke = 'black',
        private float $strokeWidth = 1,
        private ?string $transform = null
    ) {
        // ...
    }

    public function __toString(): string {
        $attributes = $this->toSvgProps();

        return sprintf('<line %s />', $attributes);
    }
}
