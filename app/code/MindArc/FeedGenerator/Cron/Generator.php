<?php

namespace MindArc\FeedGenerator\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;

class Generator
{
    protected $feed;
    protected $feedHelper;
    protected $mediaDirectory;
    protected $logger;

    public function __construct(

        \MindArc\FeedGenerator\Model\Feed $feed,
        \MindArc\FeedGenerator\Helper\Data $feedHelper,
        \Magento\Framework\Filesystem $filesystem,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->feed = $feed;
        $this->feedHelper = $feedHelper;
        $this->logger = $logger;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function execute()
    {
        $this->logger->info('Feed generation started');

        $feedGenerated = $this->feed->generateFeed();

        $filename = $this->feedHelper->getFileName('feed');
        $path = $this->feedHelper->getFeedPath();

        $result = $this->mediaDirectory->writeFile($path . $filename, $feedGenerated);

        if (!$result) {
            $this->logger->error('The feed has not been generated');
        } else {
            $this->logger->info('Feed generation finished');
        }
    }
}