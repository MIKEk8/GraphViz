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

namespace phpDocumentor\GraphViz;

use phpDocumentor\GraphViz\Traits\AttributesSettersAndGetters;

/**
 * Represents a node/element in a GraphViz graph.
 */
class Node
{
    use AttributesSettersAndGetters;

    protected string $name;

    /**
     * Creates a new Node with a given name and optional label.
     *
     * @param string $name  Name of the node.
     * @param string|null $label Optional label for the node.
     */
    public function __construct(string $name, ?string $label = null)
    {
        $this->name = $name;

        if ($label !== null) {
            $this->setLabel($label);
        }
    }

    /**
     * Static factory method for fluent interface.
     *
     * @param string $name  Name of the node.
     * @param string|null $label Optional label for the node.
     */
    public static function create(string $name, ?string $label = null): self
    {
        return new self($name, $label);
    }

    /**
     * Sets the node's name.
     *
     * @param string $name Name of the node.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the node's name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Converts the node definition to a GraphViz-compatible string.
     */
    public function __toString(): string
    {
        $attributes = implode(', ', array_map('strval', $this->attributes));
        $name = addslashes($this->name);

        return "\"{$name}\" [{$attributes}]";
    }
}