<?php

namespace MindArc\FeedGenerator\Helper;

class Fixerio
    extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_TMP_PATH = 'Feeds/';

    protected $api;
    protected $response;

    public function __construct(
        \MindArc\FeedGenerator\Model\Fixerio $api,
        \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);
        $this->api = $api;
        $this->response = [];
    }

    public function convert($base, $currency, $amount)
    {
        if (!count($this->response)) {//it avoids always consult the services
            $this->response = $this->api->connectService();
        }
        $rates = get_object_vars($this->response->rates);
        $rate = isset($rates[$currency]) ? $rates[$currency] : 1;

        if ($base == $this->response->base) {
            $convertedAmount = $amount;
        } else {
            $convertedAmount = $amount * $rate;
        }

        return $convertedAmount;
    }
}
