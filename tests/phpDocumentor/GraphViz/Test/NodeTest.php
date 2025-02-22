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

use phpDocumentor\GraphViz\Exceptions\AttributeNotFound;
use phpDocumentor\GraphViz\Node;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Test for the class representing a GraphViz node.
 */
#[CoversClass(Node::class)]
class NodeTest extends TestCase
{
    /** @var Node */
    protected Node $fixture;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp(): void
    {
        $this->fixture = new Node('name', 'label');
    }

    /**
     * Tests the construct method
     *
     * @returnn void
     */
    public function testConstruct(): void
    {
        $fixture = new Node('MyName', 'MyLabel');
        $this->assertInstanceOf(
            Node::class,
            $fixture
        );
        $this->assertSame('MyName', $fixture->getName());
        $this->assertSame('MyLabel', $fixture->getLabel()->getValue());
    }

    /**
     * Tests the create method
     *
     * @returnn void
     */
    public function testCreate(): void
    {
        $this->assertInstanceOf(
            Node::class,
            Node::create('name', 'label')
        );
    }

    /**
     * Tests the getting and setting of the name.
     *
     */
    public function testName(): void
    {
        $this->assertSame(
            $this->fixture->getName(),
            'name',
            'Expecting the name to match the initial state'
        );
        $this->assertSame(
            $this->fixture,
            $this->fixture->setName('otherName'),
            'Expecting a fluent interface'
        );
        $this->assertSame(
            $this->fixture->getName(),
            'otherName',
            'Expecting the name to contain the new value'
        );
    }

    /**
     * Tests the magic __call method, to work as described, return the object
     * instance for a setX method, return the value for an getX method, and null
     * for the remaining method calls
     *
     */
    public function testCall(): void
    {
        $fontname = 'Bitstream Vera Sans';
        $this->assertInstanceOf(Node::class, $this->fixture->setfontname($fontname));
        $this->assertSame($fontname, $this->fixture->getfontname()->getValue());
        $this->expectException(\Exception::class);
        $this->fixture->someNonExcistingMethod();
    }

    /**
     */
    public function testGetNonExistingAttributeThrowsAttributeNotFound(): void
    {
        $this->expectException(AttributeNotFound::class);
        $this->expectExceptionMessage('Attribute with name "fontname" was not found');

        $this->fixture->getFontname();
    }

    /**
     * Tests whether the magic __toString method returns a well formatted string
     * as specified in the DOT standard
     *
     */
    public function testToString(): void
    {
        $this->fixture->setfontsize(12);
        $this->fixture->setfontname('Bitstream Vera Sans');

        $dot = '"name" [label="label", fontsize="12", fontname="Bitstream Vera Sans"]';

        $this->assertSame($dot, (string) $this->fixture);
    }

    /**
     * Tests whether the magic __toString method returns a well formatted string
     * as specified in the DOT standard when the label contains slashes.
     *
     */
    public function testToStringWithLabelContainingSlashes(): void
    {
        $this->fixture->setfontsize(12);
        $this->fixture->setfontname('Bitstream Vera Sans');
        $this->fixture->setLabel('\phpDocumentor\Descriptor\ProjectDescriptor');

        $dot = '"name" [label="\\\\phpDocumentor\\\\Descriptor\\\\ProjectDescriptor", fontsize="12", fontname="Bitstream Vera Sans"]';

        $this->assertSame($dot, (string) $this->fixture);
    }
}
