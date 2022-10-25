<?php
namespace Mindarc\GeoIp\Model;

use Exception;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Laminas\Http\PhpEnvironment\RemoteAddress;
use Psr\Log\LoggerInterface;

class GeoIp
{
    const API_URL = "geoip/settings/api_url";
    const IP_OVERRIDE = "geoip/settings/ip_override";
    const BLOCKED_COUNTRIES = "geoip/settings/blocked_countries";

    /**
     * curlClient
     *
     * @var mixed
     */
    protected $curlClient;
        
    /**
     * logger
     *
     * @var mixed
     */
    private $logger;
    
    /**
     * scopeConfigInterface
     *
     * @var mixed
     */
    private $scopeConfigInterface;

    /**
     * remoteAddress
     *
     * @var mixed
     */
    private $remoteAddress;
    
    /**
     * __construct
     *   
     * @return void
     */
    public function  __construct(
        Curl $curlClient,
        ScopeConfigInterface $scopeConfigInterface,
        RemoteAddress $remoteAddress,
        LoggerInterface $logger
    ) {
        $this->curlClient = $curlClient;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->remoteAddress = $remoteAddress;
        $this->logger = $logger;
    }
    
    /**
     * getApiUrl
     *
     * @return string
     */
    public function getApiUrl() : string
    {
        return $this->scopeConfigInterface->getValue(self::API_URL);
    }
        
    /**
     * getIpOverride
     *
     * @return void
     */
    public function getIpOverride()
    {
        return $this->scopeConfigInterface->getValue(self::IP_OVERRIDE);
    }

    
    /**
     * getBlockedCountries
     *
     * @return array
     */
    public function getBlockedCountries() : array
    {
        return explode(",",$this->scopeConfigInterface->getValue(self::BLOCKED_COUNTRIES));
    }

    /**
     * getLocationData
     *
     * @return object
     */
    public function getLocationData() {
        try {
            $ipAddress = $this->getIpAddress();
            $requestUrl = $this->getApiUrl() . $ipAddress;
            $this->curlClient->get($requestUrl);
            $result = $this->curlClient->getBody();
            
            $result = json_decode($result, true);
            return $result;

        } catch (Exception $e) {
            $this->logger->error('Error message', ['exception' => $e]);
            return null;
        }
    }
        
    /**
     * getIpAddress
     *
     * @return string
     */
    public function getIpAddress() : string
    {
        if ($this->getIpOverride() != null) {
            return $this->getIpOverride();
        }
        return $this->remoteAddress->getIpAddress();
    }
    
    /**
     * getCountryCode
     *
     * @return string
     */
    public function getCountryCode() : string
    {
        $locationData = $this->getLocationData();
        return $locationData['countryCode'];
    }
    
    /**
     * getCountry
     *
     * @return string
     */
    public function getCountry() : string
    {
        return $this->getLocationData()['country'];
    }

}
