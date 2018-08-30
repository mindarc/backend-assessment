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

		private $title, $description, $condition;

		private $productCollection, $store;

		public function __construct(
	        \Magento\Backend\Block\Template\Context $context,        
	        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
	        \Magento\Store\Model\StoreManagerInterface $store,
	        array $data = []
	    )
	    {    
	        $this->productCollection= $productCollection;
	        $this->store = $store;

	        $this->title = __("Google Product Feed");
	        $this->description =  __("Just a test feed.");
	        $this->condition = __("New");
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
	        $channel->appendChild($doc->createElement('title', $this->title));
	        $channel->appendChild($doc->createElement('link', $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)));
	        $_desc = $channel->appendChild($doc->createElement('description'));
	        $_desc->appendChild($doc->createCDATASection( $this->description ));

	        if ( $productCollection->getSize())
	        {
	        	$this->createProductXml( $productCollection, $channel, $doc );
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

				++$counter;
				if ($counter == 5) break;

	            $item = $channel->appendChild($doc->createElement('item'));

	            $item->appendChild($doc->createElement('title', $product->getName()));
	            $desc = $item->appendChild($doc->createElement('description'));
	        	$desc->appendChild($doc->createCDATASection( $product->getDescription() ));
	            $item->appendChild($doc->createElement('g:link', $product->getUrlInStore()));
	            $item->appendChild($doc->createElement('g:condition', $this->condition));

	            $price = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();

	            $item->appendChild($doc->createElement('g:price', $price));

	            try {
	                $item->appendChild($doc->createElement(
	                    'g:image_link',
	                    $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage()
	                ));
	            } catch (\Exception $e) {

	            };

        	}
		}
	}