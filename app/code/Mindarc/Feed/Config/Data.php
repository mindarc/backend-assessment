<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mindarc\Feed\Config;

use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Data
 * @package Mindarc\Feed\Config
 */
class Data extends \Magento\Framework\Config\Data
{
    /**
     * Data constructor.
     * @param \Magento\Framework\Config\ReaderInterface $reader
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param string $cacheId
     * @param SerializerInterface|null $serializer
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $reader,
        \Magento\Framework\Config\CacheInterface $cache,
        $cacheId = 'feed_config',
        SerializerInterface $serializer = null
    ) {
        parent::__construct($reader, $cache, $cacheId, $serializer);
    }
}
