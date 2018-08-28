<?php
namespace MyCompany\IpRestrict\Block\Product;
 
class CountryCode extends \Magento\Framework\View\Element\Template
{
	protected $modelCountry = null;
	
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
        \MyCompany\IpRestrict\Model\Country $country,
		array $data = []
    ) {
        $this->modelCountry = $country;
		parent::__construct($context, $data);
    }
    public function getCountryCode()
    {
		return $this->modelCountry->getCountryCode();;
    }
}