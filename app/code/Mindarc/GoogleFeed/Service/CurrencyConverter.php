<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Service;

/**
 * Class CurrencyConverter
 * @package Mindarc\GoogleFeed\Service
 */
class CurrencyConverter
{
    /**
     * currency convert url
     */
    const FIXER_CONVERT_URL = 'https://free.currencyconverterapi.com/api/v6/convert';

    /**
     * fixer api key store config path
     */
    const XML_PATH_FIXER_API_KEY = 'google_feed/converter/api_key';

    /**
     * curl connection timeout
     */
    const CURL_CONNECTION_TIMEOUT = '15';

    /**
     * curl reponse timeout
     */
    const CURL_REPONSE_TIMEOUT = '15';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Fixer constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * get the currency conversion rate from the API
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return int|mixed
     */
    public function getRate($fromCurrency = 'AUD', $toCurrency = 'USD')
    {
        $error = '';
        $rate = 0;
        try {
            $ch = curl_init();
            if (false === $ch) {
                $error = __('failed to initialize service');
            }

            $apiKey = $this->scopeConfig->getValue(
                self::XML_PATH_FIXER_API_KEY
            );

            if (!empty($apiKey)) {
                curl_setopt(
                    $ch,
                    CURLOPT_URL,
                    self::FIXER_CONVERT_URL .
                    __('?q=%1_%2&compact=ultra&apiKey=%3', $fromCurrency, $toCurrency, $apiKey)
                );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_REPONSE_TIMEOUT);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CURL_CONNECTION_TIMEOUT);

                $content = curl_exec($ch);
                if ($content === false) {
                    $error = __('Service not responding');
                }

                if ($content) {
                    $rate = json_decode($content, true);
                    $rate = $rate[sprintf('%s_%s', $fromCurrency, $toCurrency)];
                } else {
                    $error = __('Encode Error');
                }
            }
            curl_close($ch);
        } catch (\Exception $e) {
            $error = sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage());
        }

        return $rate;
    }

    /**
     * convert the amount according to the rate
     *
     * @param $amount
     * @param $rate
     * @return float|int
     */
    public function convert($amount, $rate)
    {
        return $amount * $rate;
    }
}
