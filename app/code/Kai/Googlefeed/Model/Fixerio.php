<?php
namespace Kai\Googlefeed\Model;

/**
 * class Fixerio
 *
 * Get converted currency from fixer io api
 *
 * @author Cheng Shea kai <gabazoo@gmail.com>
 * @see https://github.com/bugcskai/
 */
class Fixerio 
{
    const FIXERIO_API_ENDPOINT = 'http://data.fixer.io/api/latest?';

    const FIXERIO_ACCESS_KEY = 'd8e49f3516f3481b959a80bd05b7194b'; //ideally should be in enviroment variable

    private $base_currency;
    
    private $converted_currency;
    
    private $api_base_currency; 
    
    private $rates;

    private $httpClient;

    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->httpClient = $curl;
        $this->converted_currency = 'USD';
        $this->base_currency = 'AUD';
    }


    /**
    * Get currency in the target format
    *
    * Free plan of fixerio only allows base conversion from randomly it seems currently.
    *
    * @param float $amount amount to convert
    * @param string $currency currency to change to 
    * @param string $base_currency currency to change from 
    * @return float price
    */
    public function convertPrice(
        $amount, 
        $currency =     '',
        $base_currency =         ''
    )
    {
        $this->base_currency = (!empty($base_currency)) ? $base_currency : $this->base_currency;
        $this->converted_currency = (!empty($currency)) ? $currency : $this->converted_currency;

        try {
            $rates = $this->getFixerioRates();
        } catch (\Exception $e) {
            return 0; //stop converting.
        }

        if ($this->api_base_currency != $this->base_currency)
        {
            $rate =  $rates->{$this->converted_currency} / $rates->{$this->base_currency};
        } else {
            $rate = $rates->{$this->converted_currency};
        }

        return number_format($amount * $rate, 2);
    
    }

    /**
    * Get rates
    * 
    * @return object rate object, refer to fixerio
    */
    private function getFixerioRates()
    {
        if ($this->rates) 
        {
            return $this->rates;
        }

        $params = $this->buildParameters();

        $url = self::FIXERIO_API_ENDPOINT . http_build_query($params);

        $this->httpClient->get($url);

        try {
            $response = json_decode($this->httpClient->getBody());    

            $this->api_base_currency = $response->base;
            $this->rates = $response->rates;

            return $this->rates;

        } catch (\Exception $e) {
            $this->logger->error(__('Problem with Fixerio'));
            $this->logger->error($e->getMessage());

            throw $e;
        }    
    }

    /**
    * Generate parameters
    *
    * @return array
    */
    private function buildParameters()
    {
        $symbols = implode(',', [ $this->base_currency, $this->converted_currency ]);

        return [
            "access_key" => self::FIXERIO_ACCESS_KEY,
            "format" => 1,
        ];
    }


}
