<?php

namespace Maantje\Charts\Annotations;

trait HasYAxis {
    public function yAxis(): ?string {
        return $this->yAxis;
    }

    public function setYAxis(string $yAxis): static {
        $this->yAxis = $yAxis;

        return $this;
    }
}
