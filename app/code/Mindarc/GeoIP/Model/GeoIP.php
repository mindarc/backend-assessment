<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GeoIP
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GeoIP\Model;

/**
 * Class GeoIP
 * @package Mindarc\GeoIP\Model
 */
class GeoIP
{
    /**
     * ip check service
     */
    const GEO_IP_SERVICE_URL = 'http://ip-api.com/json/';

    /**
     * curl connection timeout
     */
    const CURL_CONNECTION_TIMEOUT = '15';

    /**
     * curl reponse timeout
     */
    const CURL_RESPONSE_TIMEOUT = '15';

    /**
     * @var array
     */
    private $countryData;

    /**
     * get country code
     *
     * @return mixed|null
     */
    public function getCountryCode()
    {
        if (empty($this->countryData)) {
            $this->getUserCountry();
        }
        if (!empty($this->countryData['countryCode'])) {
            return $this->countryData['countryCode'];
        }
        return null;
    }

    /**
     * get and store country data
     */
    private function getUserCountry()
    {
        $ip = $this->getIP();
        $this->countryData = $this->getCountryByIP($ip);
    }

    /**
     * get the IP address of the user
     *
     * @return string
     */
    private function getIP()
    {
        $ip = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * retrieve country data for the ip
     *
     * @param $ip
     * @return mixed|string
     */
    private function getCountryByIP($ip)
    {
        $country = '';
        try {
            $ch = curl_init();
            if (false === $ch) {
                $error = __('failed to initialize service');
            }

            curl_setopt($ch, CURLOPT_URL, self::GEO_IP_SERVICE_URL . $ip);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_RESPONSE_TIMEOUT);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CURL_CONNECTION_TIMEOUT);

            $content = curl_exec($ch);
            if ($content === false) {
                $error = __('Service not responding');
            }

            if ($content) {
                $country = json_decode($content, true);
            } else {
                $error = __('Encode Error');
            }
            curl_close($ch);
        } catch (\Exception $e) {
            $error = sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage());
        }

        return $country;
    }
}
