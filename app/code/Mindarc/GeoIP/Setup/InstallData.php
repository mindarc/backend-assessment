<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GeoIP
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GeoIP\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Mindarc\GeoIP\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * InstallData constructor.
     * @param BlockFactory $modelBlockFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory
    ) {
        $this->blockFactory = $modelBlockFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $cmsBlocks = [
            [
                'title' => 'Product Information - US',
                'identifier' => 'product_information_us',
                'content' => '<div>Static block content for US users</div>',

            ],
            [
                'title' => 'Product Information - Global',
                'identifier' => 'product_information_global',
                'content' => '<div>Static block content for Global users</div>',
            ],

        ];

        foreach ($cmsBlocks as $data) {
            $cmsBlock = $this->blockFactory->create();
            $cmsBlock->getResource()->load($cmsBlock, $data['identifier']);
            if (!$cmsBlock->getData()) {
                $cmsBlock->setData($data);
            } else {
                $cmsBlock->addData($data);
            }
            $cmsBlock->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID]);
            $cmsBlock->setIsActive(1);
            $cmsBlock->save();
        }

        $setup->endSetup();
    }
}
