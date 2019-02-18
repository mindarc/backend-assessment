<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Model\ResourceModel\Catalog;

use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Store\Model\Store;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product\Visibility;

/**
 * Class Product
 * @package Mindarc\GoogleFeed\Model\ResourceModel\Catalog
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * image no selection
     */
    const NOT_SELECTED_IMAGE = 'no_selection';

    /**
     * Collection Zend Db select
     *
     * @var \Magento\Framework\DB\Select
     */
    private $select;

    /**
     * Attribute cache
     *
     * @var array
     */
    private $attributesCache = [];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productResource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    private $productVisibility;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $productModel;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $catalogImageHelper;

    /**
     * Product constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Visibility $productVisibility
     * @param null $connectionName
     * @param \Magento\Catalog\Model\Product|null $productModel
     * @param \Magento\Catalog\Helper\Image|null $catalogImageHelper
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        $connectionName = null,
        \Magento\Catalog\Model\Product $productModel = null,
        \Magento\Catalog\Helper\Image $catalogImageHelper = null
    ) {
        $this->productResource = $productResource;
        $this->storeManager = $storeManager;
        $this->productVisibility = $productVisibility;
        $this->productModel = $productModel ?: ObjectManager::getInstance()->get(\Magento\Catalog\Model\Product::class);
        $this->catalogImageHelper = $catalogImageHelper ?: ObjectManager::getInstance()
            ->get(\Magento\Catalog\Helper\Image::class);
        parent::__construct($context, $connectionName);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_entity', 'entity_id');
    }

    /**
     * Get category collection array
     *
     * @param $exportProfile
     * @return array|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getCollection($exportProfile)
    {
        $storeId = $exportProfile->getStoreId();

        $products = [];

        /* @var $store Store */
        $store = $this->storeManager->getStore($storeId);
        if (!$store) {
            return false;
        }

        $connection = $this->getConnection();

        // product visibilities to select ['catalog', 'search', 'catalog,search']
        $productVisibility = [
            Visibility::VISIBILITY_IN_CATALOG,
            Visibility::VISIBILITY_IN_SEARCH,
            Visibility::VISIBILITY_BOTH,
        ];

        // array to collect selects from flat table [default select]
        $select = ['entity_id'];

        // feed element attributes
        $feedElements = $exportProfile->getFeedElements();
        if (!empty($feedElements)) {
            $attributes = [];
            foreach ($feedElements as $element) {
                // if magento attribute assigned to the element
                if (!empty($element['magento'])) {
                    // magento attribute code
                    $attributeCode = $element['magento'];
                    if (in_array($attributeCode, $attributes)) {
                        continue;
                    }
                    $attributes[$element['element']] = $attributeCode;

                    // add attribute to select
                    $select[] = $this->addSelect($attributeCode);
                }
            }
        }

        $this->select = $connection->select()->from(
            ['e' => $this->getTable('catalog_product_flat_' . $storeId)],
            $select
        )->joinLeft( // url rewrite
            ['url_rewrite' => $this->getTable('url_rewrite')],
            'e.entity_id = url_rewrite.entity_id AND url_rewrite.is_autogenerated = 1 AND url_rewrite.metadata IS NULL'
            . $connection->quoteInto(' AND url_rewrite.store_id = ?', $store->getId())
            . $connection->quoteInto(' AND url_rewrite.entity_type = ?', ProductUrlRewriteGenerator::ENTITY_TYPE),
            ['url' => 'request_path']
        )->where('e.visibility IN (?)', $productVisibility);// visibility filter
        $query = $connection->query($this->select);

        while ($row = $query->fetch()) {
            $product = $this->prepareProduct($row, $store->getId());
            $products[$product->getId()] = $product;
        }

        return $products;
    }

    /**
     * @param $attributeCode
     * @return bool|\Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addSelect($attributeCode)
    {
        $attribute = $this->getAttribute($attributeCode);
        if ($attribute['frontend_input'] == 'select') {
            $attributeCode = $attributeCode . '_value as ' . $attributeCode;
        }

        return $attributeCode;
    }

    /**
     * Get attribute data by attribute code
     *
     * @param $attributeCode
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAttribute($attributeCode)
    {
        if (!isset($this->attributesCache[$attributeCode])) {
            $attribute = $this->productResource->getAttribute($attributeCode);

            $this->attributesCache[$attributeCode] = [
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id' => $attribute->getId(),
                'table' => $attribute->getBackend()->getTable(),
                'is_global' => $attribute->getIsGlobal() ==
                    \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'backend_type' => $attribute->getBackendType(),
                'frontend_input' => $attribute->getFrontendInput()
            ];
        }
        return $this->attributesCache[$attributeCode];
    }

    /**
     * Prepare product
     *
     * @param array $productRow
     * @param $storeId
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function prepareProduct(array $productRow, $storeId)
    {
        $product = new \Magento\Framework\DataObject();

        $product['id'] = $productRow[$this->getIdFieldName()];
        if (empty($productRow['url'])) {
            $productRow['url'] = 'catalog/product/view/id/' . $product->getId();
        }
        $product->addData($productRow);
        $this->loadProductImages($product, $storeId);

        return $product;
    }

    /**
     * Load product images
     *
     * @param \Magento\Framework\DataObject $product
     * @param int $storeId
     * @return void
     */
    private function loadProductImages($product, $storeId)
    {
        // Get product images
        $imagesCollection = [];
        if ($product->getImage() && $product->getImage() != self::NOT_SELECTED_IMAGE) {
            $imagesCollection = [
                new \Magento\Framework\DataObject(
                    ['url' => $this->getProductImageUrl($product->getImage())]
                ),
            ];
        }

        if ($imagesCollection) {
            // Determine thumbnail path
            $thumbnail = $product->getThumbnail();
            if ($thumbnail && $product->getThumbnail() != self::NOT_SELECTED_IMAGE) {
                $thumbnail = $this->getProductImageUrl($thumbnail);
            } else {
                $thumbnail = $imagesCollection[0]->getUrl();
            }

            $product->setImages(
                new \Magento\Framework\DataObject(
                    ['collection' => $imagesCollection, 'title' => $product->getName(), 'thumbnail' => $thumbnail]
                )
            );
        }
    }

    /**
     * Get product image URL from image filename and path
     *
     * @param string $image
     * @return string
     */
    private function getProductImageUrl($image)
    {
        $productObject = $this->productModel;
        $imgUrl = $this->catalogImageHelper
            ->init($productObject, 'product_page_image_large')
            ->setImageFile($image)
            ->getUrl();

        return $imgUrl;
    }
}
