<?php
namespace Mindarc\GeoIp\Block\Product\View;

use Exception;
use Magento\Cms\Api\GetBlockByIdentifierInterface as BlockInterface;
use Mindarc\GeoIp\Model\GeoIp;
use Magento\Cms\Model\ResourceModel\Block as BlockResource;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Country extends \Magento\Framework\View\Element\Template
{    
    /**
     * geoIp
     *
     * @var mixed
     */
    protected $geoIp;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        BlockInterface $blockInterface,
        StoreManagerInterface $storeManagerInterface,
        LoggerInterface $logger,
        GeoIp $geoIp,
        array $data = []
    ) {
        $this->geoIp = $geoIp;
        $this->blockInterface = $blockInterface;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }
            
    /**
     * getStaticBlock
     *
     * @return object
     */
    public function getStaticBlock()
    {
        try {
            $identifier = $this->getStaticBlockIdentifier();
            $storeId = $this->storeManagerInterface->getStore()->getId();
            $block  = $this->blockInterface->execute($identifier, $storeId);
            return $block;
        } catch (Exception $e) {
            $this->logger->error('Error message', ['exception' => $e]);
            return null;
        }
    }
    
    /**
     * getCmsBlockContent
     *
     * @return string
     */
    public function getCmsBlockContent() : string
    {
        $staticBlock = $this->getStaticBlock();

        if($staticBlock && $staticBlock->isActive()){
            return $staticBlock->getContent();
        }

        return __('Static block content not found');
    }
    
    /**
     * getStaticBlockIdentifier
     *
     * @return string
     */
    public function getStaticBlockIdentifier() : string
    {
        if ($this->getCountryCode() == "US") {
            $cms_block = "us_static_block";
        } else {
            $cms_block = "global_static_block";
        }
        return $cms_block;
    }
    /**
     * getCountryCode
     *
     * @return string
     */
    public function getCountryCode() : string
    {
        return $this->geoIp->getCountryCode();
    }
    
    /**
     * getCountry
     *
     * @return string
     */
    public function getCountry() : string
    {
        return $this->geoIp->getCountry();
    }
}
