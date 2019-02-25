<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mindarc\Feed\Config;

use Magento\Framework\Config\SchemaLocatorInterface;

/**
 * Class SchemaLocator
 * @package Mindarc\Feed\Config
 */
class SchemaLocator implements SchemaLocatorInterface
{
    /**
     * @var \Magento\Framework\Config\Dom\UrnResolver
     */
    protected $urnResolver;

    /**
     */
    public function __construct(\Magento\Framework\Config\Dom\UrnResolver $urnResolver)
    {
        $this->urnResolver = $urnResolver;
    }

    /**
     * Get path to merged config schema
     *
     * @return string|null
     */
    public function getSchema()
    {
        return $this->urnResolver->getRealPath('urn:magento:module:Mindarc_Feed:etc/feed.xsd');
    }

    /**
     * Get path to pre file validation schema
     *
     * @return string|null
     */
    public function getPerFileSchema()
    {
        return $this->urnResolver->getRealPath('urn:magento:module:Mindarc_Feed:etc/feed.xsd');
    }
}
