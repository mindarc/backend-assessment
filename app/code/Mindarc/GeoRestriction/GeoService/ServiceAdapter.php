<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mindarc\GeoRestriction\GeoService;

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
    const GEO_RESTRICT_PATH = 'geo_section/general/service_key';

    /**
     * @var string
     */
    private $service = 'http://api.ipstack.com/';

    /**
     * @var string
     */
//    Test Key = '5778b0157bd3c96c755418a0d1f08f0b';
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
     * @param string $ip
     * @return string
     */
    public function getCountryByIP(string $ip) : string
    {
        $arguments = '?access_key=' . $this->getServiceKey() . '&output=json';
        $response = (string)$this->client->get(urlencode($ip) . $arguments)->getBody();
        if ($response) {
            $jsonObj = json_decode($response);
            if ($jsonObj && !empty($jsonObj->country_code)) {
                return $jsonObj->country_code;
            }
        }
        return '';
    }
}