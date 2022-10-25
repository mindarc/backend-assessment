<?php
namespace Mindarc\GoogleFeed\Model;

use Exception;
use Laminas\Escaper\Escaper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;

class FeedGenerator
{    

    const FEED_FILENAME = 'googlefeed/settings/feed_filename';
    const PRODUCT_FEED_TITLE = 'googlefeed/settings/feed_title';
    const PRODUCT_FEED_DESCRIPTION = 'googlefeed/settings/feed_description';
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        FilterGroup $filterGroup,
        Visibility $visibility,
        Status $productStatus,
        SearchCriteriaInterface $searchCriteriaInterface,
        StoreManagerInterface $storeManagerInterface,
        ScopeConfigInterface $scopeConfigInterface,
        StockRegistryInterface $stockRegistry,
        ProductRepository $productRepository,
        DirectoryList $dir,
        Escaper $escaper,
        LoggerInterface $logger
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->filterGroup = $filterGroup;
        $this->visibility = $visibility;
        $this->productStatus = $productStatus;
        $this->searchCriteria = $searchCriteriaInterface;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->stockRegistry = $stockRegistry;
        $this->productRepository = $productRepository;
        $this->dir = $dir;
        $this->visibility = $visibility;
        $this->logger = $logger;
        $this->escaper = $escaper;
    }
        
    /**
     * generateFeed
     *
     * @return void
     */
    public function generateFeed()
    {
        $products = $this->getAllProducts();
        $feed = $this->feedHeaders();

        foreach ($products as $product) {
            $feed .= $this->productFormat($product);
        }
        $feed .= $this->feedFooter();
        $this->saveGoogleFeed($feed);
        $this->logger->info($feed);

    }

    private function saveGoogleFeed($feed)
    {
        try {
            $path = $this->dir->getPath('pub');
            $file = $path . "/" . $this->getFeedFileName();
            file_put_contents($file, $feed);
        } catch (Exception $e) {
            $this->logger->error('Error message', ['exception' => $e]);
        }
    }
    
    /**
     * getAllProducts
     *
     * @return array
     */
    protected function getAllProducts()
    {
        $this->filterGroup->setFilters([
            $this->filterBuilder
                ->setField('status')
                ->setConditionType('in')
                ->setValue($this->productStatus->getVisibleStatusIds())
                ->create(),
            $this->filterBuilder
                ->setField('visibility')
                ->setConditionType('in')
                ->setValue($this->visibility->getVisibleInSiteIds())
                ->create(),
        ]);

        $this->searchCriteria->setFilterGroups([$this->filterGroup]);
        $products = $this->productRepository->getList($this->searchCriteria);
        $productItems = $products->getItems();

        return $productItems;
    }
    
    /**
     * productFormat
     *
     * @param  mixed $product
     * @return string
     */
    private function productFormat ($product) : string
    {
        $productData = '<item>';
        $productData .= '<g:id>' . $product->getSku() . '</g:id>';
        $productData .= '<g:title>' . $product->getName() . '</g:title>';(
        $productData .= '<g:description>' . $this->escaper->escapeHtml($product->getDescription() ? $product->getDescription() : '') . '</g:description>';
        $productData .= '<g:link>' . $product->getProductUrl() . '</g:link>';
        $productData .= '<g:image_link>' . $this->getProductImageUrl($product) . '</g:image_link>';
        $productData .= '<g:condition>new</g:condition>';
        $productData .= '<g:availability>' . $this->getStockStatus($product) . '</g:availability>';
        $productData .= '<g:price>' . $this->getProductPriceString($product) . '</g:price>';

        $productData .= '</item>';
        return $productData;
    }

    
    /**
     * feedHeaders
     *
     * @return string
     */
    private function feedHeaders() :string
    {
        $headers = '';
        $headers .= '<?xml version="1.0"?>';
        $headers .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">';
        $headers .= '<channel>';
        $headers .= '<title>Sample Product Feed - Sample Store</title>';
        $headers .= '<link>' . $this->getStoreUrl() . '</link>';
        $headers .= '<description>' . $this->getProductFeedDescription() . '</description>';
        return $headers;
    }
        
    /**
     * feedFooter
     *
     * @return string
     */
    private function feedFooter() : string 
    {
        $footer = '</channel>';
        $footer .= '</rss>';
        return $footer;
    }
    /**
     * getStoreUrl
     *
     * @return string
     */
    public function getStoreUrl() : string
    {
        return $this->storeManagerInterface->getStore()->getBaseUrl();
    }
    
    /**
     * getProductFeedTitle
     *
     * @return string
     */
    public function getProductFeedTitle() : string 
    {
        return $this->scopeConfigInterface->getValue(self::PRODUCT_FEED_TITLE);
    }
    
    /**
     * getFeedFileName
     *
     * @return string
     */
    public function getFeedFileName() : string
    {
        return $this->scopeConfigInterface->getValue(self::FEED_FILENAME);
    }
    
    /**
     * getProductFeedDescription
     *
     * @return string
     */
    public function getProductFeedDescription() : string 
    {
        return $this->scopeConfigInterface->getValue(self::PRODUCT_FEED_DESCRIPTION);
    }
    
    /**
     * getProductImageUrl
     *
     * @param  mixed $product
     * @return string
     */
    public function getProductImageUrl($product) : string
    {
        $mediaUrl = $this->storeManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $productImgUrl = $mediaUrl . $product->getImage();
        return $productImgUrl;
    }
    
    /**
     * getStockStatus
     * https://support.google.com/merchants/answer/6324448?hl=en
     * @param  mixed $product
     * @return string
     */
    public function getStockStatus($product) : string
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        $isInStock = $stockItem ? $stockItem->getIsInStock() : false;
        if ($isInStock) return "in_stock";
        return "out_of_stock";
    }
    
    /**
     * getProductPrice
     * https://support.google.com/merchants/answer/6324371?hl=en
     * @param  mixed $product
     * @return string
     */
    public function getProductPriceString($product) : string
    {
        return $product->getFinalPrice() . " " . $this->storeManagerInterface->getStore()->getCurrentCurrency()->getCode();
    }
}