<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mindarc\GeoRestriction\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class RestrictContentByCountryObserver
 * @package Mindarc\GeoRestriction\Observer
 */
class RestrictContentByCountryObserver implements ObserverInterface
{
    /**
     * string
     */
    const GEO_RESTRICT_PATH = 'geo_section/general/restrict_countries';

    /**
     * @var \Mindarc\GeoRestriction\GeoService\ServiceHelper
     */
    protected $serviceHelper;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;

    /**
     * RestrictContentByCountryObserver constructor.
     * @param \Mindarc\GeoRestriction\GeoService\ServiceHelper $serviceHelper
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     */
    public function __construct(
        \Mindarc\GeoRestriction\GeoService\ServiceHelper $serviceHelper,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->serviceHelper = $serviceHelper;
        $this->responseFactory = $responseFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->actionFlag = $actionFlag;
    }

    /**
     * Check user country and restrict access
     *
     * @param EventObserver $observer
     * @return RestrictContentByCountryObserver
     */
    public function execute(EventObserver $observer) : RestrictContentByCountryObserver
    {
        if ($observer->getRequest()->getModuleName() != 'admin' &&
            strpos($observer->getRequest()->getUriString(), 'geo-restrict/restrict/error') !== true) {
            $restrictCountries = $this->scopeConfig->getValue(self::GEO_RESTRICT_PATH,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->storeManager->getStore()->getId());
            $restrictCountriesArr = explode(',', $restrictCountries);
            if (!empty($restrictCountries) && in_array($this->serviceHelper->getUserCountry(), $restrictCountriesArr)
            ) {
                $this->responseFactory->create()->setRedirect('geo-restrict/restrict/error')->sendResponse();
            }
        }

        return $this;
    }
}