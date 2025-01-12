<?php

namespace Maantje\Charts\SVG;

use Stringable;
use Maantje\Charts\Svgable;

readonly class Polygon implements Stringable {
    use Svgable;

    /**
     * @param  array<int, array{float, float}>  $points
     */
    public function __construct(
        private array $points = [],
        private string $fill = 'black',
        private string $stroke = 'none',
        private float $strokeWidth = 0,
        private string $pointerEvents = 'none',
        private ?string $transform = null
    ) {
        // ...
    }

    public function __toString(): string {
        $attributes = $this->toSvgProps();

        // $pointsString = implode(' ', array_map(fn($point) => implode(',', $point), $this->points));

        // $attributes = sprintf(
        //     'points="%s" fill="%s" stroke="%s" stroke-width="%s" pointer-events="%s"',
        //     htmlspecialchars($pointsString, ENT_QUOTES),
        //     htmlspecialchars($this->fill, ENT_QUOTES),
        //     htmlspecialchars($this->stroke, ENT_QUOTES),
        //     $this->strokeWidth,
        //     htmlspecialchars($this->pointerEvents, ENT_QUOTES),
        // );

        // if ($this->transform) {
        //     $attributes .= sprintf(' transform="%s"', htmlspecialchars($this->transform, ENT_QUOTES));
        // }

        return sprintf('<polygon %s />', $attributes);
    }
}
