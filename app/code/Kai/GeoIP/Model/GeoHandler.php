<?php
namespace Kai\GeoIP\Model;

/**
 * class GeoHandler
 *
 * Does the IP checks, geolocation checks, and heavy lifting, requires http://ipstack.com API
 *
 * @author Cheng Shea kai <gabazoo@gmail.com>
 * @see https://github.com/bugcskai/
 */
class GeoHandler
{
    const IPSTACK_URL = 'http://api.ipstack.com/';

    private $httpClient;

    private $scopeConfig;
    
    private $logger;

    public function __construct( 
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->httpClient = $curl;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }


    /**
     * Get The location of the current user
     *
     * @return string country code in ISO 3166-A2 format
     */
    public function getUserCountryCode(){

        $ipAddress = $this->getIPAddress();

        $params = $this->buildParameters();

        $url = self::IPSTACK_URL . $ipAddress . '?' . http_build_query($params);
        
        $this->httpClient->get($url);

        try {
            $response = json_decode( $this->httpClient->getBody() );    
            return $response->country_code;        
        } catch (\Exception $e) {
            $this->logger->error(__('Problem with ipstack'));
            $this->logger->error( $e->getMessage() );
        }    

    }
    
    /**
     * Return IP Address of user
     *
     * @return string IP address
     */
    public function getIPAddress() {
        $ipaddress = '';
        
     if (array_key_exists('HTTP_CLIENT_IP', $_SERVER))
         $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
     else if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
         $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
     else if (array_key_exists('HTTP_X_FORWARDED', $_SERVER))
         $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
     else if (array_key_exists('HTTP_FORWARDED_FOR', $_SERVER))
         $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
     else if (array_key_exists('HTTP_FORWARDED', $_SERVER))
         $ipaddress = $_SERVER['HTTP_FORWARDED'];
     else if (array_key_exists('REMOTE_ADDR', $_SERVER))
         $ipaddress = $_SERVER['REMOTE_ADDR'];

     return $ipaddress;
    }

    private function buildParameters() {
        $api_key = $this->scopeConfig->getValue( 'geoip/general/geoapikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );

        return [    
            'access_key' => $api_key,
            'format' => '1',
        ];
    }

}
