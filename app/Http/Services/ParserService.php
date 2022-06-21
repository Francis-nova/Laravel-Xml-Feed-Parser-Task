<?php

namespace App\Http\Services;

use App\Dto\XmlStateDto;
use JetBrains\PhpStorm\Pure;

class ParserService
{
    public function __construct(private XmlStateDto $xmlStateDto){}

    public function convertXmlToJson(string $url): array{
        $handle = fopen($url,"r");
        $content = $this->isolateFirstNode($handle);

        return $this->parseToJson($content);
    }

    private function parseToJson(string $xmlString): array{
        $xmlString = str_replace('&lt;![CDATA[','<![CDATA[',$xmlString);
        $xmlString = str_replace(']]&gt;',']]>',$xmlString);
        $xml = simplexml_load_string($xmlString,"SimpleXMLElement",LIBXML_NOCDATA);
        $json = json_encode($xml);

        if(!$this->xmlStateDto->getInitialParentTag()) {
            $this->xmlStateDto->setLevel($this->xmlStateDto->getLevel()+1);
        }

        return [
            $this->xmlStateDto->getInitialParentTag() => json_decode($json,true)
        ];
    }

    private function isolateFirstNode($handle): string{
        while(!feof($handle)){
            $this->readAndAppend($handle);
            if($this->xmlStateDto->getByte() == "<"){
                $this->readAndAppend($handle);
                $isClosingTag = $this->checkIfClosingTag();
                $isStartingTag = $this->checkIfStartingTag();
                $this->xmlStateDto->resetCurrentTag();

                if($this->xmlStateDto->getByte() == "!"){
                    while(!($this->xmlStateDto->getByte() == ">" && $this->xmlStateDto->getPrevByte() == "]")) {
                        $this->readAndAppend($handle);
                    }
                } else {
                    while($this->xmlStateDto->getByte() !== ">") {
                        $this->xmlStateDto->appendToCurrentTag($this->xmlStateDto->getByte());
                        $this->readAndAppend($handle);
                    }
                }

                if($this->checkExitCondition($isStartingTag)) {
                    break;
                }

                $this->transferBuffer($isStartingTag,$isClosingTag);
                $this->setLevel($isStartingTag,$isClosingTag);
                $this->setInitialTag($isStartingTag);
            }
        }
        fclose($handle);
        $missingTag = $this->xmlStateDto->getInitialParentTag() ? "</{$this->xmlStateDto->getInitialParentTag()}>" : '';
        return $this->xmlStateDto->getXml().$missingTag;
    }

    private function readAndAppend($handle): void{
        $this->xmlStateDto->setPrevByte($this->xmlStateDto->getByte());
        $this->xmlStateDto->setByte(fread($handle,1));
        $this->xmlStateDto->appendToBuffer($this->xmlStateDto->getByte());
    }

    #[Pure] private function checkIfClosingTag(): bool{
        return $this->xmlStateDto->getByte() == "/";
    }

    #[Pure] private function checkIfStartingTag(): bool{
        return $this->xmlStateDto->getByte() != "/" && $this->xmlStateDto->getByte() != "!" && $this->xmlStateDto->getByte() != "?";
    }

    private function checkExitCondition(bool $isStartingTag): bool{
        if($isStartingTag && str_contains($this->xmlStateDto->getXml(),"<{$this->xmlStateDto->getCurrentTag()}>")){
            return true;
        }

        return false;
    }

    private function setLevel(bool $isStartingTag, bool $isClosingTag): void{
        $this->xmlStateDto->setLevel(
            $isStartingTag ? $this->xmlStateDto->getLevel()+1 :
                ($isClosingTag ? $this->xmlStateDto->getLevel()-1 : $this->xmlStateDto->getLevel())
        );
    }

    private function transferBuffer(bool &$isStartingTag, bool &$isClosingTag): void{
        if(!str_contains($this->xmlStateDto->getBuffer(),'<source')){
            $shouldTransfer = true;
            if(str_contains($this->xmlStateDto->getBuffer(),'<publisher')){
                $shouldTransfer = false;
                $isStartingTag = false;
            }
            if(str_contains($this->xmlStateDto->getBuffer(),'</publisher')){
                $shouldTransfer = false;
                $isClosingTag = false;
            }

            if($shouldTransfer){
                $this->xmlStateDto->appendToXml($this->xmlStateDto->getBuffer());
            }
        } else {
            $isStartingTag = false;
        }
        $this->xmlStateDto->resetBuffer();
    }

    private function setInitialTag(bool $isStartingTag): void{
        if($isStartingTag){
            $this->xmlStateDto->setInitialParentTag($this->xmlStateDto->getCurrentTag());
        }
    }

}
