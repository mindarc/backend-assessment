<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mindarc\GeoRestriction\Setup;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterfaceFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 * @package Mindarc\GeoRestriction\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var BlockInterfaceFactory
     */
    private $blockInterfaceFactory;

    /**
     * UpgradeData constructor.
     * @param BlockRepositoryInterface $blockRepository
     * @param BlockInterfaceFactory $blockInterfaceFactory
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        BlockInterfaceFactory $blockInterfaceFactory
    ) {
        $this->blockRepository = $blockRepository;
        $this->blockInterfaceFactory = $blockInterfaceFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $usBlock = $this->blockInterfaceFactory->create()->setTitle('US Block')
                ->setIdentifier('us-block')
                ->setContent('US Block Content')
                ->setIsActive(1)->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID]);

            $this->blockRepository->save($usBlock);

            $globalBlock = $this->blockInterfaceFactory->create()->setTitle('Global Block')
                ->setIdentifier('global-block')
                ->setContent('Global Block Content')
                ->setIsActive(1)->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID]);

            $this->blockRepository->save($globalBlock);
        }

        $setup->endSetup();
    }
}