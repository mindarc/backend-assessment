<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mindarc\GeoRestriction\Controller\Restrict;

/**
 * Class Error
 * @package Mindarc\GeoRestriction\Controller\Restrict
 */
class Error extends \Magento\Framework\App\Action\Action
{
    protected $response;

    /**
     * Error constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->response = $response;
        parent::__construct($context);
    }

    /**
     * @param null $coreRoute
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute($coreRoute = null) : \Magento\Framework\App\ResponseInterface
    {
        $this->response->setHttpResponseCode('404');
        $this->response->setBody('Access Forbidden');

        return $this->response;
    }
}