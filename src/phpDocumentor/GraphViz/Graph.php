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

use InvalidArgumentException;
use phpDocumentor\GraphViz\Exceptions\Exception;
use phpDocumentor\GraphViz\Traits\AttributesSettersAndGetters;

/**
 * Represents a GraphViz graph (main graph or subgraph).
 *
 * In case of a subgraph:
 * When the name of the subgraph is prefixed with _cluster_ then the contents
 * of this graph will be grouped and a border will be added. Otherwise it is
 * used as logical container to place defaults in.
 *
 * @method Graph setRankSep(string $rankSep)
 * @method Graph setCenter(string $center)
 * @method Graph setRank(string $rank)
 * @method Graph setRankDir(string $rankDir)
 * @method Graph setSplines(string $splines)
 * @method Graph setConcentrate(string $concentrate)
 */
class Graph
{
    use AttributesSettersAndGetters;

    /** @var string Name of this graph */
    protected string $name = 'G';

    /** @var GraphType Type of this graph; may be digraph, graph or subgraph */
    protected GraphType $type = GraphType::DIGRAPH;

    /** @var bool If the graph is strict then multiple edges are not allowed between the same pairs of nodes */
    protected bool $strict = false;

    /** @var Graph[] A list of subgraphs for this Graph */
    protected array $graphs = [];

    /** @var Node[] A list of nodes for this Graph */
    protected array $nodes = [];

    /** @var Edge[] A list of edges / arrows for this Graph */
    protected array $edges = [];

    /** @var string The path to execute dot from */
    protected string $path = '';

    /**
     * Factory method to instantiate a Graph so that you can use fluent coding
     * to chain everything.
     *
     * @param string $name The name for this graph.
     * @param bool $directional Whether this is a directed or undirected graph.
     *
     * @return Graph
     */
    public static function create(string $name = 'G', bool $directional = true): self
    {
        $graph = new self();
        $graph->setName($name)
            ->setType($directional ? GraphType::DIGRAPH : GraphType::GRAPH);

        return $graph;
    }

    /**
     * Sets the path for the execution. Only needed if it is not in the PATH env.
     *
     * @param string $path The path to execute dot from
     */
    public function setPath(string $path): self
    {
        $realpath = realpath($path);
        if ($realpath) {
            $this->path = $realpath . DIRECTORY_SEPARATOR;
        }

        return $this;
    }

    /**
     * Sets the name for this graph.
     *
     * If this is a subgraph you can prefix the name with _cluster_ to group all
     * contained nodes and add a border.
     *
     * @param string $name The new name for this graph.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the name for this Graph.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the type for this graph.
     *
     * @param string|GraphType $type Must be either "digraph", "graph" or "subgraph".
     *
     * @throws InvalidArgumentException If $type is not "digraph", "graph" or
     * "subgraph".
     */
    public function setType(string|GraphType $type): self
    {
        if (is_string($type)) {
            $type = GraphType::tryFrom($type);
            if ($type === null) {
                throw new InvalidArgumentException('Type must be "digraph", "graph", or "subgraph".');
            }
        }

        $this->type = $type;

        return $this;
    }


    /**
     * Returns the type of this Graph.
     */
    public function getType(): string
    {
        return $this->type->value;
    }

    /**
     * Set if the Graph should be strict. If the graph is strict then
     * multiple edges are not allowed between the same pairs of nodes
     */
    public function setStrict(bool $isStrict): self
    {
        $this->strict = $isStrict;

        return $this;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    /**
     * Adds a subgraph to this graph; automatically changes the type to subgraph.
     *
     * Please note that an index is maintained using the name of the subgraph.
     * Thus if you have 2 subgraphs with the same name that the first will be
     * overwritten by the latter.
     *
     * @param Graph $graph The graph to add onto this graph as
     * subgraph.
     * @see Graph::create()
     *
     */
    public function addGraph(self $graph): self
    {
        $graph->setType(GraphType::SUBGRAPH);
        $this->graphs[$graph->getName()] = $graph;

        return $this;
    }

    /**
     * Checks whether a graph with a certain name already exists.
     *
     * @param string $name Name of the graph to find.
     */
    public function hasGraph(string $name): bool
    {
        return isset($this->graphs[$name]);
    }

    /**
     * Returns the subgraph with a given name.
     *
     * @param string $name Name of the requested graph.
     */
    public function getGraph(string $name): self
    {
        return $this->graphs[$name];
    }

    /**
     * Sets a node in the $nodes array; uses the name of the node as index.
     *
     * Nodes can be retrieved by retrieving the property with the same name.
     * Thus 'node1' can be retrieved by invoking: $graph->node1
     *
     * @param Node $node The node to set onto this Graph.
     * @see Node::create()
     *
     */
    public function setNode(Node $node): self
    {
        $this->nodes[$node->getName()] = $node;

        return $this;
    }

    /**
     * Finds a node in this graph or any of its subgraphs.
     *
     * @param string $name Name of the node to find.
     */
    public function findNode(string $name): ?Node
    {
        if (isset($this->nodes[$name])) {
            return $this->nodes[$name];
        }

        foreach ($this->graphs as $graph) {
            $node = $graph->findNode($name);
            if ($node) {
                return $node;
            }
        }

        return null;
    }

    /**
     * Sets a node using a custom name.
     *
     * @param string $name Name of the node.
     * @param Node $value Node to set on the given name.
     * @see Graph::setNode()
     *
     */
    public function __set(string $name, Node $value): void
    {
        $this->nodes[$name] = $value;
    }

    /**
     * Returns the requested node by its name.
     *
     * @param string $name The name of the node to retrieve.
     * @see Graph::setNode()
     *
     */
    public function __get(string $name): ?Node
    {
        return $this->nodes[$name] ?? null;
    }

    /**
     * Links two nodes to eachother and registers the Edge onto this graph.
     *
     * @param Edge $edge The link between two classes.
     * @see Edge::create()
     *
     */
    public function link(Edge $edge): self
    {
        $this->edges[] = $edge;

        return $this;
    }

    /**
     * Exports this graph to a generated image.
     *
     * This is the only method that actually requires GraphViz.
     *
     * @link http://www.graphviz.org/content/output-formats
     * @uses GraphViz/dot
     *
     * @param string $type The type to export to; see the link above for a
     *     list of supported types.
     * @param string $filename The path to write to.
     *
     * @throws Exception If an error occurred in GraphViz.
     */
    public function export(string $type, string $filename): self
    {
        $tmpfile = (string)tempnam(sys_get_temp_dir(), 'gvz');
        file_put_contents($tmpfile, (string)$this);

        $output = [];
        $code = 0;
        exec($this->path . "dot -T" . escapeshellarg($type) . " -o" . escapeshellarg($filename) . " " . escapeshellarg($tmpfile) . " 2>&1", $output, $code);
        unlink($tmpfile);

        if ($code !== 0) {
            throw new Exception('GraphViz error: ' . implode(PHP_EOL, $output));
        }

        return $this;
    }

    /**
     * Generates a DOT file for use with GraphViz.
     *
     * GraphViz is not used in this method; it is safe to call it even without
     * GraphViz installed.
     */
    public function __toString(): string
    {
        $elements = array_merge($this->graphs, $this->attributes, $this->edges, $this->nodes);
        $content = implode(PHP_EOL, array_map('strval', $elements));

        $strict = $this->strict ? 'strict ' : '';

        return "{$strict}{$this->getType()} \"{$this->getName()}\" {\n{$content}\n}";
    }
}
