<?php

declare(strict_types=1);

namespace phpDocumentor\GraphViz;

/**
 * Represents a single GraphViz attribute.
 */
class Attribute
{
    protected string $key;
    protected string $value;

    /**
     * Creates a new attribute.
     *
     * @param string $key   Attribute name.
     * @param string $value Attribute value.
     */
    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Sets the attribute key.
     *
     * @param string $key Attribute name.
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Gets the attribute key.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Sets the attribute value.
     *
     * @param string $value Attribute value.
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Gets the attribute value.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Converts the attribute to a GraphViz-compatible string.
     */
    public function __toString(): string
    {
        $key = $this->key === 'url' ? 'URL' : $this->key;
        $value = $this->value;

        if ($this->isValueContainingSpecials()) {
            $value = '"' . $this->encodeSpecials() . '"';
        } elseif (!$this->isValueInHtml()) {
            $value = '"' . addslashes($value) . '"';
        }

        return "{$key}={$value}";
    }

    /**
     * Checks if the value contains HTML.
     */
    public function isValueInHtml(): bool
    {
        return str_starts_with($this->value, '<');
    }

    /**
     * Checks if the value contains special characters needing escaping.
     */
    public function isValueContainingSpecials(): bool
    {
        return str_contains($this->value, '\\');
    }

    /**
     * Encodes special characters in the value.
     *
     * @see http://www.graphviz.org/doc/info/attrs.html#k:escString
     */
    protected function encodeSpecials(): string
    {
        return preg_replace('/(\'|"|\\x00|\\\\(?![\\\\NGETHLnlr]))/', '\\\\$0', $this->value);
    }
}
