<?php
/**
 * phpDocumentor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   phpDocumentor\GraphViz\Tests
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\GraphViz\Test;

use phpDocumentor\GraphViz\AttributeNotFound;
use phpDocumentor\GraphViz\Node;
use PHPUnit\Framework\TestCase;

/**
 * Test for the the class representing a GraphViz node.
 *
 * @package phpDocumentor\GraphViz\Tests
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class NodeTest extends TestCase
{
    /** @var Node */
    protected $fixture = null;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp()
    {
        $this->fixture = new Node('name', 'label');
    }

    /**
     * Tests the construct method
     *
     * @covers \phpDocumentor\GraphViz\Node::__construct
     *
     * @returnn void
     */
    public function testConstruct()
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
     * @covers \phpDocumentor\GraphViz\Node::create
     *
     * @returnn void
     */
    public function testCreate()
    {
        $this->assertInstanceOf(
            Node::class,
            Node::create('name', 'label')
        );
    }

    /**
     * Tests the getting and setting of the name.
     *
     * @covers \phpDocumentor\GraphViz\Node::getName
     * @covers \phpDocumentor\GraphViz\Node::setName
     */
    public function testName()
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
     * @covers \phpDocumentor\GraphViz\Node::__call
     * @covers \phpDocumentor\GraphViz\Node::getAttribute
     * @covers \phpDocumentor\GraphViz\Node::setAttribute
     */
    public function testCall()
    {
        $fontname = 'Bitstream Vera Sans';
        $this->assertInstanceOf(Node::class, $this->fixture->setfontname($fontname));
        $this->assertSame($fontname, $this->fixture->getfontname()->getValue());
        $this->assertNull($this->fixture->someNonExistingMethod());
    }

    /**
     * @covers \phpDocumentor\GraphViz\Node::getAttribute
     * @covers \phpDocumentor\GraphViz\AttributeNotFound::__construct
     */
    public function testGetNonExistingAttributeThrowsAttributeNotFound()
    {
        $this->expectException(AttributeNotFound::class);
        $this->expectExceptionMessage('Attribute with name "fontname" was not found');

        $this->fixture->getFontname();
    }

    /**
     * Tests whether the magic __toString method returns a well formatted string
     * as specified in the DOT standard
     *
     * @covers \phpDocumentor\GraphViz\Node::__toString
     */
    public function testToString()
    {
        $this->fixture->setfontsize(12);
        $this->fixture->setfontname('Bitstream Vera Sans');

        $dot = <<<DOT
"name" [
label="label"
fontsize="12"
fontname="Bitstream Vera Sans"
]
DOT;

        $this->assertSame($dot, (string) $this->fixture);
    }

    /**
     * Tests whether the magic __toString method returns a well formatted string
     * as specified in the DOT standard when the label contains slashes.
     *
     * @covers \phpDocumentor\GraphViz\Node::__toString
     */
    public function testToStringWithLabelContainingSlashes()
    {
        $this->fixture->setfontsize(12);
        $this->fixture->setfontname('Bitstream Vera Sans');
        $this->fixture->setLabel('\phpDocumentor\Descriptor\ProjectDescriptor');

        $dot = <<<DOT
"name" [
label="\\\\phpDocumentor\\\\Descriptor\\\\ProjectDescriptor"
fontsize="12"
fontname="Bitstream Vera Sans"
]
DOT;

        $this->assertSame($dot, (string) $this->fixture);
    }
}
