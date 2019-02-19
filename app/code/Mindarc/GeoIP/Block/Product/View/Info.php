<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GeoIP
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GeoIP\Block\Product\View;

/**
 * Class Info
 * @package Mindarc\GeoIP\Model
 */
class Info implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Mindarc\GeoIP\Model\GeoIP
     */
    private $geoIP;

    /**
     * Info constructor.
     * @param \Mindarc\GeoIP\Model\GeoIP $geoIP
     */
    public function __construct(
        \Mindarc\GeoIP\Model\GeoIP $geoIP
    ) {
        $this->geoIP = $geoIP;
    }

    /**
     * is the accessing user from US
     *
     * @return bool
     */
    public function isUsUser()
    {
        $countryCode = $this->geoIP->getCountryCode();
        return $countryCode == 'US';
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $countryCode = $this->geoIP->getCountryCode();
    }
}
