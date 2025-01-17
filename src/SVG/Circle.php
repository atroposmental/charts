<?php

namespace Maantje\Charts\SVG;

use Stringable;
use Maantje\Charts\Svgable;

readonly class Circle implements Stringable {
    use Svgable;

    public function __construct(
        private float $cx = 0,
        private float $cy = 0,
        private float $r = 50,
        private string $fill = 'black',
        private string $stroke = 'none',
        private float $strokeWidth = 0,
        public mixed $title = '',
        private ?string $transform = null
    ) {
        // ...
    }

    public function __toString(): string {
        $attributes = $this->toSvgProps();

        // $attributes = sprintf(
        //     'cx="%s" cy="%s" r="%s" fill="%s" stroke="%s" stroke-width="%s"',
        //     $this->cx,
        //     $this->cy,
        //     $this->r,
        //     htmlspecialchars($this->fill, ENT_QUOTES),
        //     htmlspecialchars($this->stroke, ENT_QUOTES),
        //     $this->strokeWidth
        // );

        // if ($this->transform) {
        //     $attributes .= sprintf(' transform="%s"', htmlspecialchars($this->transform, ENT_QUOTES));
        // }

        return sprintf('<circle %s><title>%s</title></circle>', $attributes, htmlspecialchars($this->title, ENT_QUOTES));
    }
}
