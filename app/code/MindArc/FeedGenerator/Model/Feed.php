<?php

namespace MindArc\FeedGenerator\Model;

class Feed
{
    const PRODUCT_FIELDS = [
        'sku',
        'name',
        'description',
        'final_price',
        'price',
        'image',
        'url'
    ];

    protected $title;

    protected $description;

    protected $condition;

    protected $productCollection;

    protected $store;

    protected $helper;

    protected $fixerio;

    protected $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MindArc\FeedGenerator\Helper\Fixerio $fixerio,
        \MindArc\FeedGenerator\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        $this->fixerio = $fixerio;
        $this->productCollection = $productCollection;
        $this->store = $store;
        $this->helper = $helper;
        $this->title = 'The name of your data feed';
        $this->description = 'A description of your content';
    }

    protected function prepareCollection()
    {
        return $this->productCollection->create()->addFieldToSelect('*')->setPageSize(6)->load();
    }

    public function generateFeed()
    {
        $content = $this->generateHeadFeed();
        $dom = $content['dom'];
        $rss = $content['rss'];

        $channel = $rss->appendChild($dom->createElement('channel'));
        $channel->appendChild($dom->createElement('title', $this->title));
        $channel->appendChild($dom->createElement('link', $this->helper->getWebLink()));
        $description = $channel->appendChild($dom->createElement('description'));
        $description->appendChild($dom->createCDATASection($this->description));

        $items = $this->prepareCollection();

        $this->generateItems($items, $channel, $dom);

        return $dom->saveXML();
    }

    protected function generateHeadFeed()
    {
        $dom = new \DOMDocument('1.0');
        $dom->formatOutput = true;
        $rss = $dom->appendChild($dom->createElement('rss'));
        $rss->setAttribute('version', '2.0');
        $rss->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');

        return array('dom' => $dom, 'rss' => $rss);
    }

    protected function generateItems($items, $channel, $dom)
    {
        foreach ($items as $product) {

            $item = $channel->appendChild($dom->createElement('item'));

            $item->appendChild($dom->createElement('title', $product->getName()));
            $item->appendChild($dom->createElement('link', $product->getProductUrl()));

            $desc = $item->appendChild($dom->createElement('description'));
            $desc->appendChild($dom->createCDATASection($product->getDescription()));

            try {
                $item->appendChild($dom->createElement('g:image_link', $this->helper->getImageLink($product)));
            } catch (\Exception $e) {
                $item->appendChild($dom->createElement('g:image_link', __('Product no image')));
            }
            $item->appendChild($dom->createElement('g:price', $this->helper->getPrice($product)));

            $item->appendChild($dom->createElement('g:condition', $this->helper->getProductAttribute($product)));

            $item->appendChild($dom->createElement('g:id', $product->getSku()));

            $storeCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
            $priceConverted = $this->getConvertedPrice($storeCode, 'USD', $product->getPrice());

            $item->appendChild($dom->createElement('g:converted_price', $priceConverted));

        }
    }

    protected function getConvertedPrice($base, $currency, $price)
    {
        return bcdiv($this->fixerio->convert($base, $currency, $price), 1, 2);
    }
}