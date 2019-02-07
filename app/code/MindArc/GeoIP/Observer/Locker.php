<?php

namespace MindArc\GeoIP\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Locker
    implements ObserverInterface
{
    protected $helper;

    protected $request;

    private $responseFactory;

    protected $actionFlag;

    private $url;

    protected $isDenied = false;

    protected $_storeManagerInterface;

    public function __construct(
        \MindArc\GeoIP\Helper\GeoIP $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\UrlInterface $url
    )
    {
        $this->helper = $helper;
        $this->request = $request;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->actionFlag = $actionFlag;
    }

    public function execute(Observer $observer)
    {
        $ip = $this->helper->getIpAddress();
        $blackList = $this->helper->getBlackListCodes();
        if (!$blackList || empty($blackList)) {
            return $this;
        }
        $blackList = explode(',', $blackList);
        $isoCountry = $this->helper->getIsoCountry($ip);

        $lockedCountry = $this->lockCountry($isoCountry, $blackList);
        if ($lockedCountry) {
            $this->denyCustomerAccess($observer);
        }

        return $this;

    }

    protected function denyCustomerAccess($observer)
    {
        $response = $observer->getControllerAction()->getResponse();

        $response->clearBody()->setStatusCode(\Magento\Framework\App\Response\Http::STATUS_CODE_403);
        $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
    }

    protected function lockCountry($isoCountry, $blackList)
    {
        if ($blackList) {
            foreach ($blackList as $iso) {
                if (strtolower($isoCountry) === strtolower($iso)) {
                    return true;
                }
            }
        }

        return false;
    }
}