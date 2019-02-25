<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mindarc\Feed\FeedAdapter;

/**
 * Class GeneratorFactory
 * @package Mindarc\Feed\FeedAdapter
 */
class GeneratorFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     *
     * @param string $feedClassName
     * @param array $data
     * @return \Mindarc\Feed\FeedAdapter\GeneratorInterface
     */
    public function create($feedClassName = '', array $data = []) : \Mindarc\Feed\FeedAdapter\GeneratorInterface
    {
        if (empty($feedClassName)) {
            throw new \InvalidArgumentException(
                $feedClassName .
                'No Feed Generator Class provided'
            );
        }
        $feed = $this->_objectManager->create($feedClassName, $data);
        if (false == $feed instanceof \Mindarc\Feed\FeedAdapter\GeneratorInterface) {
            throw new \InvalidArgumentException(
                $feedClassName .
                ' doesn\'t implement \Mindarc\Feed\FeedAdapter\GeneratorInterface'
            );
        }

        return $feed;
    }
}
