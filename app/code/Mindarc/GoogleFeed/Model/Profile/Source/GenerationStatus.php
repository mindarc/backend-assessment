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
 * Class GenerationStatus
 * @package Mindarc\GoogleFeed\Model\Profile\Source
 */
class GenerationStatus implements OptionSourceInterface
{
    /**
     * Master data statuses
     */
    const STATUS_NEW = 0;
    const STATUS_RUNNING = 1;
    const STATUS_ERROR = 2;
    const STATUS_FINISHED = 3;

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
            self::STATUS_NEW => __('NEW'),
            self::STATUS_RUNNING => __('RUNNING'),
            self::STATUS_ERROR => __('ERROR'),
            self::STATUS_FINISHED => __('FINISHED'),
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
