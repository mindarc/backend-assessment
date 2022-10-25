<?php
namespace Mindarc\GeoIp\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mindarc\GeoIp\Model\GeoIp;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlInterface;

class Restriction implements ObserverInterface
{
    protected $geoIp;
    protected $redirectFactory;

    public function __construct(
        GeoIp $geoIp,
        RedirectFactory $redirect,
        UrlInterface $url
    )
    {
        $this->geoIp = $geoIp;
        $this->redirect = $redirect;
        $this->url = $url;
    }

    public function execute(Observer $observer)
    {
        $countryCode = $this->geoIp->getCountryCode();

        if (in_array($countryCode, $this->geoIp->getBlockedCountries())) {
            $action = $observer->getControllerAction();
            $response = $action->getResponse();
            $response->clearBody()->setStatusCode(\Magento\Framework\App\Response\Http::STATUS_CODE_503);
            $norouteUrl = $this->url->getDirectUrl('errors/404.php');
            $response->setRedirect($norouteUrl);
            return;
        }
    }
}