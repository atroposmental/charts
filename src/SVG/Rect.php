<?php

namespace Maantje\Charts\SVG;

use Stringable;
use Maantje\Charts\Svgable;

readonly class Rect implements Stringable {
    use Svgable;

    public function __construct(
        private float $x = 0,
        private float $y = 0,
        private float $width = 100,
        private float $height = 100,
        private string $fill = 'black',
        private float $fillOpacity = 1,
        private string $stroke = 'none',
        private float $strokeWidth = 0,
        private float $rx = 0,
        private float $ry = 0,
        public mixed $title = '',
        private ?string $transform = null,
        private ?array $additional = null
    ) {
        // ...
    }

    public function __toString(): string {
        $attributes = $this->toSvgProps();

        return sprintf('<rect %s><title>%s</title></rect>', $attributes, htmlspecialchars($this->title, ENT_QUOTES));
    }
}
