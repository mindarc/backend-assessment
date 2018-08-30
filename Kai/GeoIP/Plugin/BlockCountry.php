<?php

namespace Kai\GeoIP\Plugin;


/**
 * Plugin BlockCountry
 *
 * Blocks user if from designated countries, wrap around controller dispatch, 
 * try to block it as higher level as possible to prevent extra load to the server
 * https://devdocs.magento.com/guides/v2.2/extension-dev-guide/routing.html
 *
 * @author Cheng Shea kai <gabazoo@gmail.com>
 * @see https://github.com/bugcskai/
 */
class BlockCountry
{
	private $geoHandler, $logger;

	private $restrictedCountries;

	public function __construct(
		\Kai\GeoIP\Model\GeoHandler $geoHandler,
		\Psr\Log\LoggerInterface $logger
	){
		$this->geoHandler = $geoHandler;
		$this->logger = $logger;

		$this->restrictedCountries = ['RU','CN'];
	}

	
	public function aroundDispatch(
		\Magento\Framework\App\FrontControllerInterface	$subject,
		callable										$proceed,
		\Magento\Framework\App\RequestInterface			$request
	) {
		
		$countryCode = $this->geoHandler->getUserCountryCode();

		if ( in_array($countryCode, $this->restrictedCountries) )
		{
			$this->logger->info(__('User barred from site'.$this->geoHandler->getIPAddress()));

			throw new \Exception(__('You are blocked from accessing the site'));
		} 

		return $proceed($request);
		
	}

}