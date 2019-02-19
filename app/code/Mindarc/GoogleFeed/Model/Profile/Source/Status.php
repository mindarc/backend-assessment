<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Model\Profile\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Mindarc\GoogleFeed\Model\Profile\Source
 */
class Status implements OptionSourceInterface
{
    /**
     * Master data statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    /**
     * Prepare Master data statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('ENABLED'),
            self::STATUS_DISABLED => __('DISABLED'),
        ];
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getStatusById($id)
    {
        return $this->getAvailableStatuses()[$id];
    }
}
