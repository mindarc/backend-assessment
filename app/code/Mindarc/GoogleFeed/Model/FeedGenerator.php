<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Model;

use Mindarc\GoogleFeed\Model\Profile\Source\GenerationStatus as Status;

/**
 * Class FeedGenerator
 * @package Mindarc\GoogleFeed\Model
 */
class FeedGenerator
{
    /**
     * store config xml path for generation enabled
     */
    const XML_PATH_GENERATION_ENABLED = 'google_feed/generate/enabled';

    /**
     * EMAIL_RECIPIENTS
     */
    const XML_PATH_ERROR_RECIPIENT = 'google_feed/generate/recipients';

    /**
     * EMAIL_SENDER
     */
    const XML_PATH_ERROR_IDENTITY = 'google_feed/generate/identity';

    /**
     * EMAIL_TEMPLATE
     */
    const XML_PATH_ERROR_TEMPLATE = 'google_feed/generate/template';

    /**
     * feed generation sub dir
     */
    const FEED_GENERATION_SUB_DIR = 'google_feed';

    /**
     * feed file format
     */
    const FEED_FILE_FORMAT = 'xml';

    /**
     * @var Profile
     */
    private $profile;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $directory;

    /**
     * @var \Magento\Framework\Filesystem\File\Write
     */
    private $stream;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceModel\Catalog\ProductFactory
     */
    private $productFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $file;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\State
     */
    private $indexerState;

    /**
     * FeedGenerator constructor.
     * @param Profile $profile
     * @param \Magento\Framework\App\State $AppState
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Config\Model\Config\Reader\Source\Deployed\DocumentRoot $documentRoot
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ResourceModel\Catalog\ProductFactory $productFactory
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param Profile\Source\Status $status
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $indexerState
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Profile $profile,
        \Magento\Framework\App\State $AppState,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Config\Model\Config\Reader\Source\Deployed\DocumentRoot $documentRoot,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mindarc\GoogleFeed\Model\ResourceModel\Catalog\ProductFactory $productFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        \Mindarc\GoogleFeed\Model\Profile\Source\Status $status,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $indexerState
    ) {
        $this->profile = $profile;
        $this->appState = $AppState;
        $this->dateTime = $dateTime;
        $this->scopeConfig = $scopeConfig;
        $this->directory = $filesystem->getDirectoryWrite($documentRoot->getPath());
        $this->storeManager = $storeManager;
        $this->productFactory = $productFactory;
        $this->priceHelper = $priceHelper;
        $this->transportBuilder = $transportBuilder;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->status = $status;
        $this->indexerState = $indexerState;
    }

    /**
     * function to call for cron and cli scopes, so the area code is simulated
     *
     * @param null $profileId
     * @return mixed
     * @throws \Exception
     */
    public function generate($profileId = null)
    {
        // Emulate the Area Code
        $areaCode = \Magento\Framework\App\Area::AREA_FRONTEND;
        return $this->appState->emulateAreaCode(
            $areaCode,
            [$this, 'process'],
            [$profileId]
        );
    }

    /**
     * run the process
     *
     * @param null $profileId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process($profileId = null)
    {
        $errors = [];

        // if feed generation is disabled by configurations
        if (!$this->isGenerationEnabled()) {
            $message = __('Feed generation is disabled by store configuration.');
            return [$message];
        }

        // if catalog product flat indexer is not enabled by configurations
        if (!$this->indexerState->isFlatEnabled()) {
            $message = __('catalog_product_flat is not and should be enabled. (Store > Configuration > Catalog > Store Front > Use Flat Catalog Product)');
            return [$message];
        }

        // get profile collection to execute
        $profileCollection = $this->profile->getProfileCollection($profileId);
        // if there are no items in the collection to execute
        if (empty($profileCollection->getSize())) {
            $message = __('No enabled profiles to run in the selection.');
            return [$message];
        }

        foreach ($profileCollection as $profile) {
            // running the profile to generate the xml
            $result = $this->runProfile($profile);

            // $result is error
            if (!is_int($result)) {
                $errors[] = $result;
            }
        }

        // if error occurs, send out emails for the nominated recipients
        if ($errors) {
            $errors[] = $this->sendErrorEmail($errors);
            return $errors;
        }


        return $errors;
    }

    /**
     * if error occurs, send out emails for the nominated recipients
     *
     * @param $errors
     * @return bool|string
     */
    private function sendErrorEmail($errors)
    {
        // get comma separated list of recipients from store config
        $recipients = $this->scopeConfig->getValue(
            self::XML_PATH_ERROR_RECIPIENT
        );

        if ($recipients) {
            try {
                $this->transportBuilder->setTemplateIdentifier(
                    $this->scopeConfig->getValue(
                        self::XML_PATH_ERROR_TEMPLATE
                    )
                )->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )->setTemplateVars(
                    ['errors' => implode("<br/>", $errors)]
                )->setFrom(
                    $this->scopeConfig->getValue(
                        self::XML_PATH_ERROR_IDENTITY
                    )
                );

                foreach (explode(',', $recipients) as $email) {
                    $this->transportBuilder->addTo(trim($email));
                }

                $transport = $this->transportBuilder->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $e) {
                return $e->getMessage();
            }
        }
        return true;
    }

    /**
     * execute individual google feed profile
     *
     * @param $profile
     * @return int|string|void
     */
    public function runProfile($profile)
    {
        $storeId = $profile->getStoreId();

        // setting store id to the store manager so the Image URL's will be taken correctly
        $this->storeManager->setCurrentStore($storeId);

        $profile->setGenerationStatus(Status::STATUS_RUNNING);
        $profile->save();

        // generate the xml feed file for the export profile
        $result = $this->generateXml($profile);

        if (is_int($result)) { // no errors in the generation process
            $profileId = $profile->getId();
            $fileName = $profile->getFilename();
            $fileFormat = self::FEED_FILE_FORMAT;
            $storeUrl = $this->storeManager->getStore($profile->getStoreId())->getBaseUrl();
            $feedLink = $storeUrl . 'media/' . self::FEED_GENERATION_SUB_DIR .
                '/' . $storeId . '_' . $profileId . '/' . $fileName . '.' . $fileFormat;
            $profile->setGenerationStatus(Status::STATUS_FINISHED)
                ->setGeneratedTime( // last generated time
                    $this->dateTime->date('Y-m-d H:i:s')
                )->setUrl( // feed public url
                    $feedLink
                );
        } else {
            $profile->setGenerationStatus(Status::STATUS_ERROR);
        }
        $profile->save();

        // Reverting back the store id in store manager
        $this->storeManager->setCurrentStore(0);

        return $result;
    }

    /**
     * @param $profile
     * @return int|string|void
     */
    private function generateXml($profile)
    {
        try {
            // generate the xml feed file for the export profile
            $this->initFeed($profile);
            $result = $this->createFeed($profile);
            $this->finalizeFeed();

            return $result;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $e->getMessage();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * initialize the xml feed file and writing the header elements to the xml feed
     *
     * @param $profile
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function initFeed($profile)
    {
        $storeId = $profile->getStoreId();

        // title element
        $title = sprintf('<title>%s</title>', $profile->getProfileName());

        // description element
        $description = sprintf('<description>%s</description>', __('Google Shopping Feed for Magento Store'));

        // link element
        $link = sprintf('<link>%s</link>', $this->storeManager->getStore($storeId)->getBaseUrl());

        // deader and starting element set
        $start = '<?xml version="1.0"?>' .
            PHP_EOL .
            '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' .
            PHP_EOL .
            '<channel>' .
            PHP_EOL .
            $title .
            PHP_EOL .
            $link .
            PHP_EOL .
            $description .
            PHP_EOL;

        $profileId = $profile->getId();
        $fileName = $profile->getFilename();
        $fileFormat = self::FEED_FILE_FORMAT;

        // relative file path from document root
        $path = 'pub/media/' . self::FEED_GENERATION_SUB_DIR .
            '/' . $storeId . '_' . $profileId . '/' . $fileName . '.' . $fileFormat;
        $this->stream = $this->directory->openFile($path);

        $fileHeader = sprintf($start);
        // writing header and starting elements into the xml file
        $this->stream->write($fileHeader);
    }

    /**
     * prepare xml content for an individual product
     *
     * @param $product
     * @param $profile
     * @return string
     */
    private function prepareProductRow($product, $profile)
    {
        $row = '';
        $feedElements = $profile->getFeedElements();
        if (!empty($feedElements)) {
            foreach ($feedElements as $feedElement) {
                $value = null;

                if (!empty($feedElement['element'])) {
                    $element = $feedElement['element'];

                    if (!empty($feedElement['default'])) {
                        $value = $feedElement['default'];
                    } elseif (!empty($feedElement['magento']) && $product->getData($feedElement['magento'])) {
                        // or get the product's magento attribute value
                        $value = $product->getData($feedElement['magento']);
                    }

                    // if the value is not empty
                    if (!empty($value)) {
                        // tweaking and formatting the values
                        switch ($element) {
                            case 'description':
                                // removing line breaks as extra line breaks can break the xml
                                $value = trim(preg_replace('/\s\s+/', ' ', $value));
                                break;
                            case 'link':
                                // format the product url
                                $value = htmlspecialchars($this->getUrl($product));
                                break;
                            case 'g:price':
                                // Price format
                                $value = $this->priceHelper->currencyByStore($value, $product['store_id'], true, false);
                                break;
                            case 'g:image_link':
                                $images = $product->getImages();
                                if ($images) {
                                    foreach ($images->getCollection() as $key => $image) {
                                        $value = $image->getUrl();
                                    }
                                }
                                break;
                        }

                        if (!empty($feedElement['cdata']) && $feedElement['cdata'] == '1') {
                            // wrap with CDATA tag, if set
                            $value = PHP_EOL . '<![CDATA[' . $value . ']]>' . PHP_EOL;
                        }

                        $row .= sprintf('<%s>%s</%s>', $element, $value, $element) . PHP_EOL;
                    }
                }
            }
        }

        return '<item>' . PHP_EOL . $row . '</item>';
    }

    /**
     * Get store base url
     *
     * @param $storeId
     * @param string $type
     * @return string
     */
    protected function getStoreBaseUrl($storeId, $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore($storeId);
        $isSecure = $store->isUrlSecure();
        return rtrim($store->getBaseUrl($type, $isSecure), '/') . '/';
    }

    /**
     * Get url
     *
     * @param $product
     * @param string $type
     * @return string
     */
    protected function getUrl($product, $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        $url = '';
        if (!empty($product['url'])) {
            $url = $product['url'];
        }
        return $this->getStoreBaseUrl($product['store_id'], $type) . ltrim($url, '/');
    }

    /**
     * @param $profile
     * @return int|void
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    private function createFeed($profile)
    {
        $products = $this->productFactory->create()->getCollection($profile);

        foreach ($products as $product) {
            // generate product data for the feed xml
            $xmlRow = $this->prepareProductRow($product, $profile);
            // writing the product row into the feed xml
            $this->writeRow($xmlRow);
        }

        return count($products);
    }

    /**
     * adding closing tags to end the feed elements
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function finalizeFeed()
    {
        if ($this->stream) {
            $end = '</channel>' .
                PHP_EOL .
                '</rss>';
            $this->stream->write(sprintf($end));
            $this->stream->close();
        }
    }

    /**
     * write individual row to feed file
     *
     * @param $row
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function writeRow($row)
    {
        $this->getStream()->write($row . PHP_EOL);
    }

    /**
     * Get file handler
     *
     * @return \Magento\Framework\Filesystem\File\WriteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStream()
    {
        if ($this->stream) {
            return $this->stream;
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('File handler unreachable'));
        }
    }

    /**
     * check if the generation is enabled from store config
     *
     * @return bool
     */
    private function isGenerationEnabled()
    {
        // check if scheduled generation enabled
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERATION_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
