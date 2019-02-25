<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mindarc\Feed\CurrencyService;

/**
 * Class ServiceHelper
 * @package Mindarc\GeoRestriction\CurrencyService
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

    protected $_cache = [];

    /**
     * ServiceHelper constructor.
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param ServiceAdapter $serviceAdapter
     */
    public function __construct(
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Mindarc\Feed\CurrencyService\ServiceAdapter $serviceAdapter
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->serviceAdapter = $serviceAdapter;
    }

    /**
     * This can be extedted to cache multiple currecy conversion based on future needs
     * @return float
     */
    public function convertPrice($price) : float
    {
        if(!isset($this->_cache['USDAUD'])){
            $this->_cache['USDAUD'] = $this->serviceAdapter->getCurrency('USD', 'AUD');
        }
        return (float) number_format($price/$this->_cache['USDAUD'], 2);
    }
}