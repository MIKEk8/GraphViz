<?php

declare(strict_types=1);

namespace phpDocumentor\GraphViz\Traits;

use Exception;
use phpDocumentor\GraphViz\Exceptions\AttributeNotFound;

trait AttributesSettersAndGetters
{
    use Attributes;

    /**
     * Magic method for dynamic getters/setters of attributes.
     *
     * Supports methods like setX(), getX().
     *
     * @param string $name Method name.
     * @param array $arguments Arguments for the method.
     *
     * @throws AttributeNotFound
     * @throws \Exception
     */
    public function __call(string $name, array $arguments)
    {
        $prefix = substr($name, 0, 3);
        $key = strtolower(substr($name, 3));

        if ($prefix === 'set') {
            $this->setAttribute($key, (string)$arguments[0]);
            return $this;
        }

        if ($prefix === 'get') {
            return $this->getAttribute($key);
        }

        throw new Exception("Method '{$name}' not found");
    }
}
