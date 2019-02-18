<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Model\Profile;

/**
 * Class Source
 * @package Mindarc\GoogleFeed\Model\Profile
 */
abstract class Source implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [];
    }

    /**
     * @return array
     */
    public function toIndexedArray()
    {
        $options = $this->toOptionArray();
        $indexedArray = [];
        foreach ($options as $item) {
            $indexedArray[$item['value']] = $item['label'];
        }
        return $indexedArray;
    }
}
