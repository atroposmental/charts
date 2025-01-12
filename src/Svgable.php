<?php

namespace Maantje\Charts;

trait Svgable {
    public function toSvgProps(?array $attributes = null, ?array $values = null): string {
        $properties = '';

        if ( is_null($attributes) ) {
            $reflector = new \ReflectionClass($this);

            $attributes = array_filter(
                array_map(function($prop) {
                    return (isset($this->{$prop->getName()}) && ! is_array($this->{$prop->getName()})) ? [$prop->getName(), $this->{$prop->getName()}] : null;
                },
                $reflector->getProperties()),
                fn($value) => isset($value[1]) && ! blank($value[1])
            );

            if ( isset($this->additional) && ! empty($this->additional) ) {
                $attributes = array_merge($attributes, array_map(fn($key, $value) => [$key, $value], array_keys($this->additional), $this->additional));
            }
        }

        foreach ($attributes as [$property, $value]) {
            if ( ! blank($value) ) {
                $properties .= sprintf('%s="%s" ', $this->camelToKebab($property), htmlspecialchars($value, ENT_QUOTES));
            }
        }

        return $properties;
    }

    public function camelToKebab(string $string): string {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
    }
}