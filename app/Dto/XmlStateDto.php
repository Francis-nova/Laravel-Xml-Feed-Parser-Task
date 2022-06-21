<?php

namespace App\Dto;



class XmlStateDto
{

    public function __construct(
        private string $xml = "",
        private string $buffer = "",
        private int $level = 0,
        private string $prevByte = '',
        private string $byte = '',
        private array $initialParentTag = [],
        private string $currentTag = '',
        private bool $insideRepetitiveNode = false,
    ){}

    public function getXml(): string
    {
        return $this->xml;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getPrevByte(): string
    {
        return $this->prevByte;
    }

    public function getByte(): string
    {
        return $this->byte;
    }

    public function getInitialParentTag(): ?string
    {
        if(array_key_exists($this->level,$this->initialParentTag)){
            return $this->initialParentTag[$this->level];
        }
        return null;
    }

    public function appendToXml(string $byte): XmlStateDto
    {
        $this->xml .= $byte;
        return $this;
    }

    public function appendToCurrentTag(string $byte): XmlStateDto
    {
        $this->currentTag .= $byte;
        return $this;
    }

    public function getCurrentTag(): string
    {
        return $this->currentTag;
    }

    public function resetCurrentTag(): XmlStateDto
    {
        $this->currentTag = "";
        return $this;
    }

    public function setLevel(int $level): XmlStateDto
    {
        $this->level = $level;
        return $this;
    }

    public function setPrevByte(string $prevByte): XmlStateDto
    {
        $this->prevByte = $prevByte;
        return $this;
    }

    public function setByte(string $byte): XmlStateDto
    {
        $this->byte = $byte;
        return $this;
    }

    public function setInitialParentTag(string $initialParentTag): XmlStateDto
    {
        $this->initialParentTag[$this->level] = $initialParentTag;
        return $this;
    }

    public function isInsideRepetitiveNode(): bool
    {
        return $this->insideRepetitiveNode;
    }

    public function setInsideRepetitiveNode(bool $insideRepetitiveNode): XmlStateDto
    {
        $this->insideRepetitiveNode = $insideRepetitiveNode;
        return $this;
    }

    public function getBuffer(): string
    {
        return $this->buffer;
    }

    public function appendToBuffer(string $byte): XmlStateDto
    {
        $this->buffer .= $byte;
        return $this;
    }

    public function resetBuffer(): XmlStateDto
    {
        $this->buffer = '';
        return $this;
    }

}
