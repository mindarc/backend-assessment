<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mindarc\GeoRestriction\GeoService;

/**
 * Class ServiceHelper
 * @package Mindarc\GeoRestriction\GeoService
 */
class ServiceHelper
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var ServiceAdapter
     */
    protected $serviceAdapter;

    /**
     * ServiceHelper constructor.
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param ServiceAdapter $serviceAdapter
     */
    public function __construct(
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Mindarc\GeoRestriction\GeoService\ServiceAdapter $serviceAdapter
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->serviceAdapter = $serviceAdapter;
    }

    /**
     * @return string
     */
    public function getUserCountry() : string
    {
        $userIp = $this->remoteAddress->getRemoteAddress();
        return $this->serviceAdapter->getCountryByIP($userIp);
    }
}