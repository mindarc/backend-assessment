<?php
namespace Mindarc\GeoIp\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{    
    /**
     * blockFactory
     *
     * @var mixed
     */
    private $blockFactory;
    
    /**
     * __construct
     *
     * @param  mixed $blockFactory
     * @return void
     */
    public function __construct(BlockFactory $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }
        
    /**
     * install
     *
     * @param  mixed $setup
     * @param  mixed $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.O.1', '<')) {

            $usCmsBlockData = [
                'title' => 'US CMS Block',
                'identifier' => 'us_static_block',
                'content' => "<h1>This is the US CMS Block</h1>",
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0
            ];

            $this->blockFactory->create()->setData($usCmsBlockData)->save();

            $globalCmsBlockData = [
                'title' => 'Global CMS Block',
                'identifier' => 'global_static_block',
                'content' => "<h1>This is the Global CMS Block</h1>",
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0
            ];
            $this->blockFactory->create()->setData($globalCmsBlockData)->save();
        }
    }
}