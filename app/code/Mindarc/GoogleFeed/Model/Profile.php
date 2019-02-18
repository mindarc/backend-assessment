<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Model;

/**
 * Class Profile
 * @package Mindarc\GoogleFeed\Model
 */
class Profile extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mindarc\GoogleFeed\Model\ResourceModel\Profile::class);
    }

    /**
     * Load Export Profile from the request
     *
     * @param null $profileId
     * @return $this
     */
    public function initProfile($profileId = null)
    {
        if ($profileId) {
            // TODO - use entity manager to load model
            $this->load($profileId);
        }
        if (!$this->_registry->registry('current_profile')) {
            $this->_registry->register('current_profile', $this);
        }
        return $this;
    }

    /**
     * @param $profileId
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProfileCollection($profileId)
    {
        $profileCollection = $this->getResourceCollection();
        if (!empty($profileId)) {
            if (is_array($profileId)) {
                $profileCollection->addFieldToFilter('entity_id', ['in' => $profileId]);
            } else {
                $profileCollection->addFieldToFilter('entity_id', $profileId);
            }
        }

        return $profileCollection->addFieldToFilter(
            'status',
            ['eq' => \Mindarc\GoogleFeed\Model\Profile\Source\Status::STATUS_ENABLED]
        );
    }

    /**
     * feed element data
     *
     * @return array
     */
    public function getFeedElements()
    {
        return [
            [
                'element' => 'title',
                'magento' => 'name',
                'cdata' => 1
            ],
            [
                'element' => 'link',
                'magento' => 'url_key',
                'cdata' => 1
            ],
            [
                'element' => 'description',
                'magento' => 'description',
                'cdata' => 1
            ],
            [
                'element' => 'g:image_link',
                'magento' => 'image',
                'cdata' => 1
            ],
            [
                'element' => 'g:price',
                'magento' => 'price',
                'cdata' => 0
            ],
            [
                'element' => 'g:condition',
                'magento' => '',
                'default' => 'New',
                'cdata' => 0
            ],
            [
                'element' => 'g:id',
                'magento' => 'sku',
                'cdata' => 0
            ],
        ];
    }
}
