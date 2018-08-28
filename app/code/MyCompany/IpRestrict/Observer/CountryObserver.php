<?php
namespace MyCompany\IpRestrict\Observer;
 
 
class CountryObserver implements \Magento\Framework\Event\ObserverInterface
 
{
	public function __construct(
        \MyCompany\IpRestrict\Model\Country $country
    ) {
        $this->modelCountry = $country;
    }
    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$objCountry = $this->modelCountry->getCountryCode();
		if($objCountry->geoplugin_countryCode=='CN' || $objCountry->geoplugin_countryCode=='RU')
		{
			echo "<h1>You are not allowed to access website</h1>";
			exit();
		}
    }
 }