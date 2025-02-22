<?php

declare(strict_types=1);

/**
 * phpDocumentor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\GraphViz\Traits;

use phpDocumentor\GraphViz\Attribute;
use phpDocumentor\GraphViz\Exceptions\AttributeNotFound;

trait Attributes
{
    /** @var Attribute[] */
    protected array $attributes = [];

    /**
     * Sets an attribute.
     *
     * @param string $name  Attribute name.
     * @param string $value Attribute value.
     */
    public function setAttribute(string $name, string $value): self
    {
        $this->attributes[$name] = new Attribute($name, $value);

        return $this;
    }

    /**
     * Gets an attribute by name.
     *
     * @param string $name Attribute name.
     *
     * @throws AttributeNotFound
     */
    public function getAttribute(string $name): Attribute
    {
        if (!isset($this->attributes[$name])) {
            throw new AttributeNotFound("Attribute '{$name}' not found.");
        }

        return $this->attributes[$name];
    }

    /**
     * Returns all attributes as an array.
     *
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
