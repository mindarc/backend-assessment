<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mindarc\Feed\GoogleFeed;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Api\StoreRepositoryInterface;
use Mindarc\Feed\FeedAdapter\GeneratorInterface;

/**
 * Class Generator
 * @package Mindarc\Feed\GoogleFeed
 */
class Generator implements GeneratorInterface
{
    /**
     * string
     */
    const FEED_FOLDER = 'feed_section/general/feed_folder';

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $productStatus;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $catalogImageHelper;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * @var \Mindarc\Feed\CurrencyService\ServiceHelper
     */
    protected $serviceHelper;

    /**
     * @var \Magento\Framework\Filesystem\File\WriteInterface
     */
    protected $stream;

    /**
     * @var bool
     */
    protected $reset = true;

    /**
     * Generator constructor.
     * @param StoreRepositoryInterface $storeRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Helper\Image $catalogImageHelper
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Mindarc\Feed\CurrencyService\ServiceHelper $serviceHelper
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Helper\Image $catalogImageHelper,
        \Magento\Framework\Filesystem $filesystem,
        \Mindarc\Feed\CurrencyService\ServiceHelper $serviceHelper
    ) {
        $this->storeRepository = $storeRepository;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->catalogImageHelper = $catalogImageHelper;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->serviceHelper = $serviceHelper;
    }

    /**
     * Generate the Google feed file
     *
     * @param string $filePrefix
     * @return mixed
     */
    public function generate(string $filePrefix)
    {
        foreach ($this->storeRepository->getList() as $store) {
            $feedTitle = 'Test Store ' . $store->getName();
            $storeUrl = $store->getCurrentUrl();
            $feedDescription = 'Test Store ' . $store->getName();

            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect(['name', 'description', 'price', 'image', 'url']);
            $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
            $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
            $collection->addStoreFilter($store);

            foreach ($collection as $product) {
                if ($this->reset) {
                    $fileName = $filePrefix . $store->getCode() . '.xml';
                    $this->createFeed($fileName, $feedTitle, $storeUrl, $feedDescription);
                }
                $this->writeFeedRow($product);
            }
            $this->finalizeFeed();
        }
    }

    /**
     * Generate Feed Headers
     *
     * @param string $fileName
     * @param string $title
     * @param string $link
     * @param string $description
     */
    protected function createFeed(string $fileName, string $title, string $link, string $description)
    {
        $path = $this->getFeedPath(). DIRECTORY_SEPARATOR . $fileName;
        $this->stream = $this->directory->openFile($path);

        $fileHeader = '<?xml version="1.0"?>' .
            PHP_EOL .
            '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' .
            PHP_EOL;
        $fileHeader .= '<channel>' . PHP_EOL;
        $fileHeader .= '<title>' . htmlspecialchars($title) . '</title>' . PHP_EOL;
        $fileHeader .= '<link>' . htmlspecialchars($link) . '</link>' . PHP_EOL;
        $fileHeader .= '<description>' . htmlspecialchars($description) . '</description>';
        $this->stream->write($fileHeader);
        $this->reset = false;
    }

    /**
     * Generate Feed contents
     *
     * @param \Magento\Catalog\Model\Product $product
     */
    protected function writeFeedRow(\Magento\Catalog\Model\Product $product)
    {
        $row = '<item>' . PHP_EOL;
        $row .= '<title>' . htmlspecialchars($product->getName()) . '</title>' . PHP_EOL;
        $row .= '<link>' . htmlspecialchars($product->getProductUrl()) . '</link>' . PHP_EOL;
        $row .= '<description>' . htmlspecialchars($product->getDescription()) . '</link>' . PHP_EOL;
        $row .= '<g:image_link>' . $this->getProductImage($product) . '</g:image_link>' . PHP_EOL;
        $row .= '<g:price>' . $product->getPrice() . '</g:price>' . PHP_EOL;
        $row .= '<g:price>' . $product->getPrice() . '</g:price>' . PHP_EOL;
        $row .= '<g:converted_price>' . $this->serviceHelper->convertPrice($product->getPrice()) . '</g:converted_price>' . PHP_EOL;
        $row .= '</item>' . PHP_EOL;
        $this->getStream()->write($row);
    }

    /**
     * Get product image url
     *
     * @param $product
     * @return string
     */
    protected function getProductImage(\Magento\Catalog\Model\Product $product) : string
    {
        return $this->catalogImageHelper
            ->init($product, 'product_page_image_large')
            ->setImageFile($product->getImage())
            ->getUrl();
    }

    /**
     * File stream
     *
     * @return \Magento\Framework\Filesystem\File\WriteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getStream() : \Magento\Framework\Filesystem\File\WriteInterface
    {
        if ($this->stream) {
            return $this->stream;
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('File handler unreachable'));
        }
    }

    /**
     * close the file
     */
    protected function finalizeFeed()
    {
        if ($this->stream) {
            $fileFooter = '</channel>' . PHP_EOL;
            $fileFooter .= '</sitemapindex>';
            $this->stream->write($fileFooter);
            $this->stream->close();
            $this->stream = null;
        }
        // Reset all counters
        $this->reset = true;
    }

    /**
     * @return string
     */
    protected function _getBaseDir(): string
    {
        return $this->directory->getAbsolutePath();
    }

    /**
     * @return string
     */
    protected function getFeedPath() : string
    {
        return $this->scopeConfig->getValue(self::FEED_FOLDER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
    }
}