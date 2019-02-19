<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GeoIP
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GeoIP\Observer;

/**
 * Class ControllerActionPreDispatchObserver
 * @package Mindarc\GeoIP\Observer
 */
class ControllerActionPreDispatchObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * scope config xml path for restriction status
     */
    const XML_PATH_GEOIP_RESTRICTION_ENABLED = 'geoip/restriction/enabled';

    /**
     * scope config xml path for restricted countries
     */
    const XML_PATH_GEOIP_RESTRICtED_COUNTRIES = 'geoip/restriction/countries';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Mindarc\GeoIP\Model\GeoIP
     */
    private $geoIP;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    private $actionFlag;

    /**
     * ControllerActionPreDispatchObserver constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mindarc\GeoIP\Model\GeoIP $geoIP
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mindarc\GeoIP\Model\GeoIP $geoIP,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->geoIP = $geoIP;
        $this->response = $response;
        $this->actionFlag = $actionFlag;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Backend\Model\View\Result\Forward|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->scopeConfig->isSetFlag(
            self::XML_PATH_GEOIP_RESTRICTION_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId())
        ) {
            // get the list of countries from the store configurations
            $restrictedCountries = $this->scopeConfig->getValue(
                self::XML_PATH_GEOIP_RESTRICtED_COUNTRIES,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->storeManager->getStore()->getId()
            );

            if (!empty($restrictedCountries)) {
                $restrictedCountries = explode(',', $restrictedCountries);
                $userCountryCode = $this->geoIP->getCountryCode();

                $requestUri = $observer->getRequest()->getUriString();
                // if the country is a restricted one and the url is not the 404 page
                if (in_array($userCountryCode, $restrictedCountries) && strpos($requestUri, 'no-route') === false) {
                    // redirect to the 404 page
                    $this->response->setRedirect('/no-route');
                    $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);

                    return;
                }
            }
        }
    }
}