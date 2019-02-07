<?php

namespace MindArc\FeedGenerator\Api;

/**
 * Interface FixerioInterface
 * @api
 */
interface FixerioInterface
{
    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return mixed
     */
    public function connectService();
}
