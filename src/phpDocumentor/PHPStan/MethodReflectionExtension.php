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

namespace phpDocumentor\GraphViz\PHPStan;

use InvalidArgumentException;
use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Graph;
use phpDocumentor\GraphViz\Node;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Type\BooleanType;
use PHPStan\Type\FloatType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use RuntimeException;
use SimpleXMLElement;

use function array_key_exists;
use function array_map;
use function file_get_contents;
use function in_array;
use function simplexml_load_string;
use function sprintf;
use function str_replace;
use function stripos;
use function strtolower;
use function substr;

final class MethodReflectionExtension implements MethodsClassReflectionExtension
{
    private const array SUPPORTED_CLASSES = [
        Node::class => 'node',
        Graph::class => 'graph',
        Edge::class => 'edge',
    ];

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (!array_key_exists($classReflection->getName(), self::SUPPORTED_CLASSES)) {
            return false;
        }

        $methods           = $this->getMethodsFromSpec(self::SUPPORTED_CLASSES[$classReflection->getName()]);
        $expectedAttribute = $this->getAttributeFromMethodName($methodName);

        return in_array($expectedAttribute, $methods, true);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        if (stripos($methodName, 'get') === 0) {
            return new AttributeGetterMethodReflection($classReflection, $methodName);
        }

        $attributeName = $this->getAttributeFromMethodName($methodName);

        return new AttributeSetterMethodReflection(
            $classReflection,
            $methodName,
            $this->getAttributeInputType($attributeName)
        );
    }

    /**
     * @return string[]
     */
    private function getMethodsFromSpec(string $className): array
    {
        $simpleXml = $this->getAttributesXmlDoc();

        $elements = $simpleXml->xpath(sprintf("xsd:complexType[@name='%s']/xsd:attribute", $className));

        if ($elements === false) {
            throw new InvalidArgumentException(
                sprintf('Class "%s" does not exist in Graphviz spec', $className)
            );
        }

        return array_map(
            static function (SimpleXMLElement $attribute): string {
                return strtolower((string) $attribute['ref']);
            },
            $elements
        );
    }

    private function getAttributeInputType(string $ref): Type
    {
        $simpleXml  = $this->getAttributesXmlDoc();
        $attributes = $simpleXml->xpath(sprintf("xsd:attribute[@name='%s']", $ref));

        if (empty($attributes)) {
            return new StringType();
        }

        $type = $attributes[0]['type'];
        $type = str_replace('xsd:', '', (string) $type);
        return match ($type) {
            'boolean' => new BooleanType(),
            'decimal' => new FloatType(),
            default => new StringType(),
        };
    }

    private function getAttributesXmlDoc(): SimpleXMLElement
    {
        $fileContent = file_get_contents(__DIR__ . '/assets/attributes.xml');

        if ($fileContent === false) {
            throw new RuntimeException('Cannot read attributes spec');
        }

        $xml = simplexml_load_string($fileContent);
        if ($xml === false) {
            throw new RuntimeException('Cannot read attributes spec');
        }

        return $xml;
    }

    private function getAttributeFromMethodName(string $methodName): string
    {
        return strtolower(substr($methodName, 3));
    }
}
