<?php

namespace Maantje\Charts;

use Closure;
use Maantje\Charts\SVG\Fragment;
use Maantje\Charts\SVG\Line;
use Maantje\Charts\SVG\Rect;
use Maantje\Charts\SVG\Text;

class XAxis implements Renderable {
    public Closure $formatter;

    /**
     * @param  float[]  $data
     * @param  Renderable[]  $annotations
     */
    public function __construct(
        public array $data = [],
        public string $title = '',
        public array $annotations = [],
        ?Closure $formatter = null,
        public array $labels = [],
        public string $lineColor = 'rgb(0,0,0,0.5)',
        public string $labelColor = 'rgb(0,0,0,0.5)'
    ) {
        $this->formatter = $formatter ?? fn(mixed $label) => number_format($label);
    }

    public function maxValue(): float {
        return max(...$this->data);
    }

    public function minValue(): float {
        return min(...$this->data);
    }

    public function render(Chart $chart): string {
        $labelCount = array_key_last($this->data);

        $svg = new Line(
            x1: $chart->left(),
            y1: $chart->bottom(),
            x2: $chart->right(),
            y2: $chart->bottom(),
            stroke: $this->lineColor
        );

        for ($i = 0; $i <= $labelCount; $i++) {
            $x = $chart->xFor($this->data[$i]);
            $y = $chart->bottom() + 25;

            $px = ($i == 0 ? $chart->left() : $chart->xFor($this->data[$i - 1]));
            $nx = ($i == $labelCount ? $chart->right() : $chart->xFor($this->data[$i + 1]));
            $xw = ($i == 0 ? $nx - $x : $x - $px) / 2;
            


            $label = $this->labels[$i] ?? $this->formatter->call($this, $this->data[$i]);
            $lineY = $chart->bottom() - 5;

            $svg .= new Fragment([
                new Text(
                    content: $label,
                    x: $x,
                    y: $y,
                    fontFamily: $chart->config->fontFamily,
                    fontSize: $chart->config->fontSize,
                    fontWeight: '600',
                    textAnchor: ($i == 0 ? 'start' : ($i == $labelCount ? 'end' : 'middle')),
                    fill: $this->labelColor
                ),
                new Line(
                    x1: $x,
                    y1: $chart->bottom(),
                    x2: $x,
                    y2: $lineY,
                    stroke: $this->lineColor
                ),
                new Rect(
                    x: ($x - ($i == 0 ? 0 : ($xw / 2))),
                    y: $chart->top(),
                    width: (in_array($i, [0, $labelCount]) ? ($xw / 2) : $xw),
                    height: $chart->availableHeight(),
                    fill: $chart->config->background,
                    additional: ['class' => 'x-axis-rect', 'data-x' => $i]
                )
            ]);
        }

        $titleX = $chart->availableWidth() / 2 + $chart->left();
        $titleY = $chart->bottom() + 40;

        $svg .= new Text(
            x: $titleX,
            y: $titleY,
            content: $this->title,
            fontFamily: $chart->config->fontFamily,
            fontSize: $chart->config->fontSize,
            textAnchor: 'end',
            fill: $this->labelColor
        );

        return $svg;
    }
}
