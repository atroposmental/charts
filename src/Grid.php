<?php

namespace Maantje\Charts;

use Maantje\Charts\SVG\Line;

readonly class Grid implements Renderable {
    public function __construct(
        public int $lines = 5,
        public string $lineColor = 'rgb(0,0,0,0.15)',
        public string $labelColor = 'rgb(0,0,0,0.5)',
        public float $lineWidth = 1
    ) {
        // ...
    }

    public function render(Chart $chart): string {
        $svg = '';
        $numLines = $this->lines;
        $lineSpacing = $chart->availableHeight() / $numLines;

        for ($i = 0; $i <= $numLines; $i++) {
            $y = $chart->bottom() - ($i * $lineSpacing);

            $line = new Line(
                x1: $chart->left(),
                y1: $y,
                x2: $chart->right(),
                y2: $y,
                stroke: $this->lineColor,
                strokeWidth: $this->lineWidth
            );

            $svg .= $line;
        }

        return $svg;
    }
}
