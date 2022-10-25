<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mindarc\GoogleFeed\Cron;

use Mindarc\GoogleFeed\Model\FeedGenerator;
use Psr\Log\LoggerInterface;

class GenerateFeed
{

    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger,
        FeedGenerator $feedGenerator
    ) {
        $this->logger = $logger;
        $this->feedGenerator = $feedGenerator;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->info("Feed Generation started");
        $this->feedGenerator->generateFeed();
        $this->logger->info("Feed Generation complete");
    }
}

