<?php

namespace MindArc\GeoIP\Block\Catalog\Product\View;

class CountryCode
    extends \Magento\Framework\View\Element\Template
{
    protected $_geoIpHelper;
    protected $_geoIP;

    public function __construct(
        \MindArc\GeoIP\Helper\GeoIP $geoIp,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->_geoIP = $geoIp;
        parent::__construct($context, $data);
    }

    public function getCountryISO()
    {
        $ip = $this->_geoIP->getIpAddress();
        $data = $this->_geoIP->getCountryData($ip);
        if ($data !== null && !empty($data['iso'])) {
            $code = $data['iso'];
        } else {
            $code = "It coudn\\'t get the country Iso code ";
        }

        return $code;
    }

    public function getBlockID()
    {
        $iso = $this->getCountryISO();

        return strtolower('geoip_' . $iso);
    }
}
