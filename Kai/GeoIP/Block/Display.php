<?php
namespace Kai\GeoIP\Block;

/**
 * class GeoHandler
 *
 * Does the IP checks, geolocation checks, and heavy lifting, requires http://ipstack.com API
 * Functions are pretty self explainatory
 *
 * @author Cheng Shea kai <gabazoo@gmail.com>
 * @see https://github.com/bugcskai/
 */
class Display extends \Magento\Framework\View\Element\Template
{
	private $geoHandler;

	private $countryCode;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Kai\GeoIP\Model\GeoHandler $geoHandler
	)
	{
		$this->geoHandler = $geoHandler;

		$this->setCurrentBlockTemplate();

		parent::__construct($context);
	}

	public function getCountryCode(){
		return $this->countryCode;
	}

	protected  function _toHtml()
	{
	    if (!$this->getTemplate()) {
	        return __('Nothing to Render');
	    }
	    return $this->fetchView($this->getTemplateFile());
	}

	private function setCurrentBlockTemplate(){
		$this->setTemplate('Kai_GeoIP::global.phtml');
	}
}