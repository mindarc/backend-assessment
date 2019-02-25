<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mindarc\Feed\CurrencyService;

use bar\foo\baz\Object;
use GuzzleHttp\Client;

/**
 * Class ServiceAdapter
 * @package Mindarc\GeoRestriction\GeoService
 */
class ServiceAdapter
{
    /**
     * string
     */
    const GEO_RESTRICT_PATH = 'feed_section/currency/service_key';

    /**
     * @var string
     */
    private $service = 'http://www.apilayer.net/api/live';

    /**
     * @var string
     */
//    Test Key '1fa6ab378043bd0f3362694564004c62';
    private $apiKey;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * ServiceAdapter constructor.
     * @param Client $client
     */
    public function __construct(
        \GuzzleHttp\Client $client,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->client = new Client(['base_uri' => $this->service]);
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    private function getServiceKey() : string
    {
        if(!$this->apiKey){
            $this->apiKey = $this->scopeConfig->getValue(self::GEO_RESTRICT_PATH,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
        }
        return $this->apiKey;
    }

    /**
     * @param string $from
     * @param string $to
     * @return Object
     */
    public function getCurrency(string $from, string $to) : float
    {
        /**
         * Need Payed service to do real conversion
         */
        $arguments = '?access_key=' . $this->getServiceKey() . '&format=1&currencies='.$to;
        $response = (string)$this->client->get($arguments)->getBody();
        if ($response) {
            $jsonObj = json_decode($response);
            if ($jsonObj && !empty($jsonObj->quotes) && isset($jsonObj->quotes->{$from.$to})) {
                return $jsonObj->quotes->{$from.$to};
            }
        }
        return 1;
    }
}