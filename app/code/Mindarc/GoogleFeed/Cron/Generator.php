<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Cron;

/**
 * Class Generator
 * @package Mindarc\GoogleFeed\Cron
 */
class Generator
{
    /**
     * @var \Mindarc\GoogleFeed\Model\FeedGenerator
     */
    private $feedGenerator;

    /**
     * Generator constructor.
     * @param \Mindarc\GoogleFeed\Model\FeedGenerator $feedGenerator
     */
    public function __construct(
        \Mindarc\GoogleFeed\Model\FeedGenerator $feedGenerator
    ) {
        parent::__construct();
        $this->feedGenerator = $feedGenerator;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $this->feedGenerator->generate();
    }
}
