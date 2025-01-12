<?php

namespace Maantje\Charts\Line;

use Maantje\Charts\Chart;
use Maantje\Charts\Renderable;
use Maantje\Charts\SVG\Fragment;
use Maantje\Charts\SVG\Path;
use Maantje\Charts\SVG\Polyline;

class Line implements Renderable {
    /**
     * @param  Point[]  $points
     */
    public function __construct(
        public readonly array $points = [],
        public readonly int $size = 5,
        public readonly ?string $yAxis = null,
        public readonly string $lineColor = 'black',
    ) {
        // ...
    }

    public function render(Chart $chart): string {
        $xSpacing = $chart->availableWidth() / array_key_last($this->points);

        $pointsSvg = '';
        $points = [];

        $minY = $chart->yForAxis($chart->minValue($this->yAxis), $this->yAxis); // Get the minimum Y-axis value

        foreach ($this->points as $index => $point) {
            $x = sprintf('%.3f', ($chart->left() + $index * $xSpacing));
            $y = $chart->yForAxis($point->y, $this->yAxis);
            $mY = sprintf('%.3f', min($y, $minY));

            $points[] = [$x, $mY];
            $pointsSvg .= $point->render($x, $mY);
        }

        return new Fragment([
            new Path(
                points: $points,
                stroke: $this->lineColor,
                strokeWidth: $this->size
            ),
            $pointsSvg,
        ]);
    }
}
