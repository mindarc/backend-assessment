<?php
namespace Kai\Googlefeed\Model;

/**
 * class Feed
 *
 * Generate google feed xml for shopping / merchant 
 *
 * @author Cheng Shea kai <gabazoo@gmail.com>
 * @see https://github.com/bugcskai/
 */
class Feed 
{

    private $feed_title;
    
    private $feed_description;
    
    private $item_condition;

    private $productCollection;
    
    private $storeManager; 
    
    private $fixerio;
    
    private $logger;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Kai\Googlefeed\Model\Fixerio $fixerio,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    )
    {    
        $this->productCollection= $productCollection;
        $this->storeManager = $storeManager;
        $this->fixerio = $fixerio;
        $this->logger = $logger;

        $this->feed_title = __("Google Product Feed");
        $this->feed_description =  __("Just a test feed.");
        $this->item_condition = __("New");
    }

    /**
    * Generate the google XML feed
    *
    * @return string XML output
    */
    public function generateFeed()
    {

        $productCollection = $this->productCollection->create();
        $productCollection->addAttributeToSelect('*');
        $productCollection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

        $doc = new \DOMDocument('1.0');
        $doc->formatOutput = true;
        $rss = $doc->appendChild($doc->createElement('rss'));
        $rss->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
        $rss->setAttribute('version', '2.0');

        $channel = $rss->appendChild($doc->createElement('channel'));
        $channel->appendChild($doc->createElement('title', $this->feed_title));
        $channel->appendChild($doc->createElement('link', $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)));
        $_desc = $channel->appendChild($doc->createElement('description'));
        $_desc->appendChild($doc->createCDATASection($this->feed_description));

        if ($productCollection->getSize())
        {
        $this->createProductXml($productCollection, $channel, $doc);
        }

        return $doc->saveXML();
    }

    /**
    * Helper function for the XML
    *
    * @return void
    */
    private function createProductXml($productCollection, $channel, $doc){
        foreach ($productCollection as $product) {

            $item = $channel->appendChild($doc->createElement('item'));

            $title = $item->appendChild($doc->createElement('title'));
            $title->appendChild($doc->createCDATASection($product->getName()));
            $desc = $item->appendChild($doc->createElement('description'));
            $desc->appendChild($doc->createCDATASection($product->getDescription()));
            $item->appendChild($doc->createElement('g:link', $product->getUrlInStore()));
            $item->appendChild($doc->createElement('g:condition', $this->item_condition));

            $price = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();

            $item->appendChild($doc->createElement('g:price', $price));

            $cprice = $this->fixerio->convertPrice($price);

            if ($cprice)
            {
                $item->appendChild($doc->createElement('g:converted_price', $cprice));
            }

            try {
                $item->appendChild($doc->createElement(
                    'g:image_link',
                    $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage()
                ));
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());

                $imageHelper = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Catalog\Helper\Image::class);
                $item->appendChild($doc->createElement(
                    'g:image_link',
                        $imageHelper->getDefaultPlaceholderUrl('image')
                ));
            };

        }
    }
}
