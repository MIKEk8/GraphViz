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

namespace phpDocumentor\GraphViz\Test;

use Mockery as m;
use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Exceptions\AttributeNotFound;
use phpDocumentor\GraphViz\Node;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

/**
 * Test for the class representing a GraphViz edge (vertex).
 */
#[CoversClass(Edge::class)]
#[CoversMethod(AttributeNotFound::class, '__construct')]
class EdgeTest extends TestCase
{
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Tests the construct method
     */
    public function testConstruct(): void
    {
        $fromNode = m::mock(Node::class);
        $toNode   = m::mock(Node::class);
        $fixture  = new Edge($fromNode, $toNode);

        $this->assertInstanceOf(
            Edge::class,
            $fixture
        );
        $this->assertSame(
            $fromNode,
            $fixture->getFrom()
        );
        $this->assertSame(
            $toNode,
            $fixture->getTo()
        );
    }

    /**
     * Tests the create method
     */
    public function testCreate(): void
    {
        $this->assertInstanceOf(
            Edge::class,
            Edge::create(new Node('from'), new Node('to'))
        );
    }

    /**
     * Tests whether the getFrom method returns the same node as passed
     * in the create method
     */
    public function testGetFrom(): void
    {
        $from = new Node('from');
        $edge = Edge::create($from, new Node('to'));
        $this->assertSame($from, $edge->getFrom());
    }

    /**
     * Tests the getTo method returns the same node as passed
     * in the create method
     */
    public function testGetTo(): void
    {
        $to   = new Node('to');
        $edge = Edge::create(new Node('from'), $to);
        $this->assertSame($to, $edge->getTo());
    }

    /**
     * Tests the magic __call method, to work as described, return the object
     * instance for a setX method, return the value for an getX method, and null
     * for the remaining method calls
     */
    public function testCall(): void
    {
        $label   = 'my label';
        $fixture = new Edge(new Node('from'), new Node('to'));
        $this->assertInstanceOf(Edge::class, $fixture->setLabel($label));
        $this->assertSame($label, $fixture->getLabel()->getValue());
        $this->expectException(\Exception::class);
        $fixture->someNonExcistingMethod();
    }

    /**
     */
    public function testGetNonExistingAttributeThrowsAttributeNotFound(): void
    {
        $fixture = new Edge(new Node('from'), new Node('to'));

        $this->expectException(AttributeNotFound::class);
        $this->expectExceptionMessage('Attribute with name "label" was not found');

        $fixture->getLabel();
    }

    /**
     * Tests whether the magic __toString method returns a well formatted string
     * as specified in the DOT standard
     */
    public function testToString(): void
    {
        $fixture = new Edge(new Node('from'), new Node('to'));
        $fixture->setLabel('MyLabel');
        $fixture->setWeight(45);

        $dot = '"from" -> "to" [label="MyLabel", weight="45"]';

        $this->assertSame($dot, (string) $fixture);
    }
}
