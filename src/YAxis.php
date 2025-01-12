<?php

namespace Maantje\Charts;

use Closure;
use Maantje\Charts\Annotations\YAxisAnnotation;
use Maantje\Charts\SVG\Text;

class YAxis implements Renderable {
    public Closure $formatter;

    /** @param  array<int, YAxisAnnotation&Renderable>  $annotations */
    public function __construct(
        public string $name = 'default',
        public string $title = '',
        public ?float $minValue = null,
        public ?float $maxValue = null,
        public int $labelMargin = 0,
        public array $annotations = [],
        public int $characterSize = 5,
        ?Closure $formatter = null,
        public string $lineColor = 'rgb(0,0,0,0.5)',
        public string $labelColor = 'rgb(0,0,0,0.5)'
    ) {
        $this->formatter = $formatter ?? fn(mixed $label) => number_format($label);

        $this->annotations = array_map(fn(YAxisAnnotation $annotation) => $annotation->setYAxis($this->name), $this->annotations);
    }

    public function render(Chart $chart): string {
        $svg = '';
        $numLines = $chart->grid->lines;
        $lineSpacing = $chart->availableHeight() / $numLines;

        $labelWidth = strlen($this->formatter->call($this, $chart->maxValue($this->name))) * $this->characterSize + $this->labelMargin;

        $chart->incrementLeftMargin($labelWidth + $chart->config->titleMargin);

        $minValue = $chart->minValue($this->name); // Get min value
        $maxValue = $chart->maxValue($this->name); // Get max value

        $valueRange = $maxValue - $minValue; // Calculate the range between min and max
        $valueStep = $valueRange / $numLines; // Calculate the step for each line

        for ($i = 0; $i <= $numLines; $i++) {
            $value = $minValue + ($valueStep * $i); // Adjust value calculation to start from min value

            $labelX = $chart->left() - 10;
            $labelY = $chart->top() + $chart->availableHeight() - ($i * $lineSpacing) + 5;
            $labelText = $this->formatter->call($this, $value);

            $svg .= new Text(
                content: $labelText,
                x: $labelX,
                y: $labelY,
                fontFamily: $chart->config->fontFamily,
                fontSize: $chart->config->fontSize,
                fontWeight: '600',
                fill: $this->labelColor,
                textAnchor: 'end'
            );
        }

        $titleY = ($chart->availableHeight()) / 2;
        $titleX = $chart->left() - $labelWidth - 25;

        $svg .= new Text(
            x: $titleX,
            y: $titleY,
            content: $this->title,
            fontFamily: $chart->config->fontFamily,
            fontSize: $chart->config->fontSize,
            fontWeight: '600',
            fill: $this->labelColor,
            textAnchor: 'middle',
            alignmentBaseline: 'middle',
            transform: "rotate(270, $titleX, $titleY)"
        );

        return $svg;
    }
}
