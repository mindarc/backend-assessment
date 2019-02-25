<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mindarc\Feed\Config;

use Magento\Framework\Config\ConverterInterface;

/**
 * Class Converter
 * @package Mindarc\Feed\Config
 */
class Converter implements ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convert($source)
    {
        $output = [];
        $xpath = new \DOMXPath($source);
        $indexers = $xpath->evaluate('/config/feed');
        /** @var $typeNode \DOMNode */
        foreach ($indexers as $indexerNode) {
            $data = [];
            $indexerId = $this->getAttributeValue($indexerNode, 'id');
            $data['indexer_id'] = $indexerId;
            $data['action_class'] = $this->getAttributeValue($indexerNode, 'class');
            $data['feedprefix'] = '';
            $data['title'] = '';
            $data['description'] = '';

            /** @var $childNode \DOMNode */
            foreach ($indexerNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                /** @var $childNode \DOMElement */
                $data = $this->convertChild($childNode, $data);
            }
            $output[$indexerId] = $data;
        }

        return $output;
    }

    /**
     * Get attribute value
     *
     * @param \DOMNode $input
     * @param string $attributeName
     * @param mixed $default
     * @return null|string
     */
    protected function getAttributeValue(\DOMNode $input, $attributeName, $default = null)
    {
        $node = $input->attributes->getNamedItem($attributeName);

        return $node ? $node->nodeValue : $default;
    }

    /**
     * Convert child from dom to array
     *
     * @param \DOMElement $childNode
     * @param array $data
     * @return array
     */
    protected function convertChild(\DOMElement $childNode, $data)
    {
        switch ($childNode->nodeName) {
            case 'feedprefix':
                $data['feedprefix'] = $childNode->nodeValue;
                break;
            case 'title':
                $data['title'] = $childNode->nodeValue;
                break;
            case 'description':
                $data['description'] = $childNode->nodeValue;
                break;
        }

        return $data;
    }
}
