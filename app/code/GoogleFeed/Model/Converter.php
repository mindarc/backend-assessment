<?php
namespace Mindarc\GoogleFeed\Model;

use Exception;
use Psr\Log\LoggerInterface;

class Converter 
{
    const FIXER_IO_URL = "https://api.apilayer.com/currency_data/convert";
    const FIXER_IO_API_KEY = "rnM9JqeR8WXYWk7CANWhHwJzVaoU0PeJ";

    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        LoggerInterface $logger
    ) {
        $this->curl = $curl;
        $this->logger = $logger;
    }

    public function convertCurrency ($price = 1, $currency = "AUD", $targetCurrency = "USD")
    {
        try {
            $curlUrl = self::FIXER_IO_URL . "?to=" . $targetCurrency. "&amount=" . $price . "&from=" . $currency;
            $this->curl->addHeader("Content-Type", "text/plain");
            $this->curl->addHeader("apikey", self::FIXER_IO_API_KEY);
            $this->curl->get($curlUrl);
            $response = $this->curl->getBody();
            $result = json_decode($response, true);
            return $result['result'] . " " . $targetCurrency;

        } catch (Exception $e) {
            $this->logger->error('Error message', ['exception' => $e]);
            return null;
        }
    }
}
