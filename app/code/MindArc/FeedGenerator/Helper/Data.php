<?php

namespace MindArc\FeedGenerator\Helper;

class Data
    extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_TMP_PATH = 'Feeds/';

    protected $storeManager;
    protected $dateTime;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
    }

    public function getImageLink($product)
    {
        $urlImage = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .
            $product->getImage();

        return $urlImage;
    }

    public function getWebLink()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    public function getPrice($product)
    {
        return $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
    }

    /*
     * This is could be a custom attribute product. Magento has not condition attribute for products
     * so you can write your custom code here
     */
    public function getProductAttribute($product, $attributeName = 'condition')
    {
        if ($attributeName == 'condition') {
            return 'new';
        } else {
            $attributeValue = $product->getData($attributeName);
            if (!empty($attributeValue)) {
                return $attributeValue;
            }
        }

        return '';

    }

    public function getFileName($filename, $format = 'xml')
    {
        $filename = $filename . '_';
        $filename .= $this->dateTime->gmtDate('d-m-Y_h:m:i');
        $filename .= '.' . $format;

        return $filename;
    }

    public function getFeedPath()
    {
        return self::XML_TMP_PATH;
    }

}
