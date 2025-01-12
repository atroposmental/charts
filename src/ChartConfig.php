<?php

namespace Maantje\Charts;

class ChartConfig {
    public function __construct(
        public float $width = 800,
        public float $height = 600,
        public ?string $background = 'white',
        public int $fontSize = 14,
        public string $fontFamily = 'arial',
        public float $leftMargin = 25,
        public float $rightMargin = 25,
        public float $bottomMargin = 50,
        public float $topMargin = 25,
        public array $margins = [],
        public string $lineColor = '#ccc',
        public float $lineWidth = 1,
        public string $labelColor = '#333',
        public int $titleMargin = 35
    ) {
        // ...
        if ( is_array($margins) && ! empty($margins) ) {
            if ( count($margins) == 2 ) {
                $this->margins = [...$margins, ...$margins];
            }
        } else {
            $this->margins = [$topMargin, $rightMargin, $bottomMargin, $leftMargin];
        }
    }

    public function inlineMargin(): string {
        return $this->margins[1] + $this->margins[3];
    }

    public function blockMargin(): string {
        return $this->margins[0] + $this->margins[2];
    }
}
