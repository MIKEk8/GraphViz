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

use phpDocumentor\GraphViz\Traits\AttributeSetterAndGetter;

/**
 * Represents an edge (connection) between two nodes in a GraphViz graph.
 */
class Edge
{
    use AttributeSetterAndGetter;

    private Node $from;
    private Node $to;

    /**
     * Creates a new edge between two nodes.
     *
     * @param Node $from Source node.
     * @param Node $to   Destination node.
     */
    public function __construct(Node $from, Node $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Factory method for fluent interface.
     *
     * @param Node $from Source node.
     * @param Node $to   Destination node.
     */
    public static function create(Node $from, Node $to): self
    {
        return new self($from, $to);
    }

    /**
     * Gets the source node.
     */
    public function getFrom(): Node
    {
        return $this->from;
    }

    /**
     * Gets the destination node.
     */
    public function getTo(): Node
    {
        return $this->to;
    }

    /**
     * Converts the edge definition to a GraphViz-compatible string.
     */
    public function __toString(): string
    {
        $attributes = implode(", ", array_map('strval', $this->attributes));
        $fromName = addslashes($this->from->getName());
        $toName = addslashes($this->to->getName());

        return "\"{$fromName}\" -> \"{$toName}\" [{$attributes}]";
    }
}
