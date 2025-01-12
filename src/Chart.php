<?php

namespace Maantje\Charts;

use Maantje\Charts\Annotations\RendersAfterSeries;
use Maantje\Charts\Annotations\RendersBeforeSeries;
use Maantje\Charts\Line\Lines;
use Maantje\Charts\Line\Point;
use Maantje\Charts\SVG\Rect;

class Chart {
    /** @var array<string, float> */
    public array $maxValue = [];

    /** @var array<string, float> */
    public array $minValue = [];

    /** @var YAxis[] */
    public array $yAxis = [];
    
    /** @var Grid */
    public readonly Grid $grid;

    /**
     * @param  Series[]  $series
     * @param  Renderable[]  $annotations
     * @param  YAxis|YAxis[]  $yAxis
     */
    public function __construct(
        public ?ChartConfig $config = null,
        Grid|null $grid = null,
        YAxis|array $yAxis = new YAxis(minValue: 0),
        public XAxis $xAxis = new XAxis,
        public array $annotations = [],
        public array $series = []
    ) {
        $this->grid = $grid ?? new Grid(lineColor: $this->config->lineColor, labelColor: $this->config->labelColor);

        $this->yAxis = array_reduce((is_array($yAxis) ? $yAxis : [$yAxis]), fn(array $carry, YAxis $yAx) => array_merge($carry, [($yAx->name ?? 'default') => $yAx]), []);

        if ( count($this->yAxis) === 0 ) {
            $this->yAxis['default'] = new YAxis('default');
        }

        if ( count($this->xAxis->data) === 0 ) {
            $this->guessXAxisData();
        }
    }

    public function renderYAxis(): string {
        return array_reduce($this->yAxis, fn($svg, $yAxis) => $svg . $yAxis->render($this), '');
    }

    public function xFor(float $x): float {
        return $this->config->leftMargin + (($x - $this->xAxis->minValue()) / ($this->xAxis->maxValue() - $this->xAxis->minValue())) * $this->availableWidth();
    }

    public function yForAxis(float $y, ?string $axis = null): float {
        return $this->config->topMargin + $this->availableHeight() - (($y - $this->minValue($axis)) / ($this->maxValue($axis) - $this->minValue($axis))) * $this->availableHeight();
    }

    public function maxValue(?string $yAxis = null): float {
        $yAxis = $yAxis ?? 'default';

        if ( array_key_exists($yAxis, $this->yAxis) && ! is_null($this->yAxis[$yAxis]->maxValue) ) {
            return $this->yAxis[$yAxis]->maxValue;
        }

        if ( array_key_exists($yAxis, $this->maxValue) ) {
            return $this->maxValue[$yAxis];
        }

        $filtered = array_filter($this->series, fn($element) => ($element->yAxis ?? 'default') === $yAxis);

        return $this->maxValue[$yAxis] = max(array_map(fn($element) => $element->maxValue(), $filtered));
    }

    public function minValue(?string $yAxis = null): float {
        $yAxis = $yAxis ?? 'default';

        if ( array_key_exists($yAxis, $this->yAxis) && ! is_null($this->yAxis[$yAxis]->minValue) ) {
            return $this->yAxis[$yAxis]->minValue;
        }

        if ( array_key_exists($yAxis, $this->minValue) ) {
            return $this->minValue[$yAxis];
        }

        $filtered = array_filter($this->series, fn($element) => ($element->yAxis ?? 'default') === $yAxis);

        return $this->minValue[$yAxis] = min(array_map(fn($element) => $element->minValue(), $filtered));
    }

    public function availableHeight(): float {
        return $this->config->height - $this->config->blockMargin();
    }

    public function availableWidth(): float {
        return $this->config->width - $this->config->rightMargin - $this->config->leftMargin;
    }

    public function top(): float {
        return $this->config->topMargin;
    }

    public function bottom(): float {
        return $this->config->height - $this->config->bottomMargin;
    }

    public function left(): float {
        return $this->config->leftMargin;
    }

    public function right(): float {
        return $this->config->width - $this->config->rightMargin;
    }

    public function incrementLeftMargin(float $value): void {
        $this->config->leftMargin += $value;
    }

    protected function background(): string {
        if ( is_null($this->config->background) ) {
            return '';
        }

        return new Rect(
            width: $this->config->width,
            height: $this->config->height,
            fill: $this->config->background,
        );
    }

    protected function guessXAxisData(): void {
        if (count($this->series) === 0) {
            return;
        }

        $firstSeries = $this->series[0];

        if ($firstSeries instanceof Lines) {
            $this->xAxis->data = array_map(fn(Point $point) => $point->x, $firstSeries->lines[0]->points);
        }
    }

    protected function renderSeries(): string {
        $svg = '';

        foreach ($this->series as $series) {
            $svg .= $series->render($this);
        }

        return $svg;
    }

    /**
     * @param  class-string  $interface
     */
    protected function renderAnnotations(string $interface): string {
        $svg = '';

        foreach ([...$this->yAxis, $this->xAxis] as $axis) {
            foreach ($axis->annotations as $annotation) {
                if (is_a($annotation, $interface)) {
                    $svg .= $annotation->render($this);
                }
            }
        }

        return $svg;
    }

    public function render(): string {
        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" viewbox="0 0 {$this->config->width} {$this->config->height}" class="svg-chart">
            {$this->background()}
            {$this->renderYAxis()}
            {$this->xAxis->render($this)}
            {$this->grid->render($this)}
            {$this->renderAnnotations(RendersBeforeSeries::class)}
            {$this->renderSeries()}
            {$this->renderAnnotations(RendersAfterSeries::class)}
        </svg>
        SVG;
    }
}
