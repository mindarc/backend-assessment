<?php

namespace MindArc\GeoIP\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{

    private $blockFactory;

    public function __construct(BlockFactory $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $cmsBlockData = [
                'title'      => 'Geo IP CMS Block',
                'identifier' => 'geoip_global_block',
                'content'    => "<h1>This is a global Geo IP Block</h1>",
                'is_active'  => 1,
                'stores'     => [0],
                'sort_order' => 0
            ];

            $block = $this->blockFactory->create();
            $block->setData($cmsBlockData)->save();

            $cmsBlockData = [
                'title'      => 'USA Geo IP CMS Block',
                'identifier' => 'geoip_us',
                'content'    => "<h1>This is a USA Geo IP Block</h1>",
                'is_active'  => 1,
                'stores'     => [0],
                'sort_order' => 0
            ];

            $block = $this->blockFactory->create();
            $block->setData($cmsBlockData)->save();
        }

        $setup->endSetup();
    }
}
