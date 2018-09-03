<?php

namespace Kai\Googlefeed\Cron;

/**
 * class RunFeed
 *
 * Cron + Logger to generate google feed into pub/media
 *
 * @author Cheng Shea kai <gabazoo@gmail.com>
 * @see https://github.com/bugcskai/
 */
class RunFeed {

  private $logger;

  private $feed;

  public function __construct(
    \Psr\Log\LoggerInterface $logger,
    \Kai\Googlefeed\Model\Feed $feed
  ) {

    $this->logger = $logger;
    $this->feed = $feed;

  }


  /**
   * Output feed to file 
   *
   * @return string XML output
   */
  public function execute() {
      $this->logger->info('Google Feed Cron Start');
    try {
      $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

      $filesystem = $_objectManager->get('Magento\Framework\Filesystem');
      $directoryList = $_objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');

      $feed = $this->feed->generateFeed();

      $media = $filesystem->getDirectoryWrite($directoryList::MEDIA);
      $media->writeFile("kaigooglefeed/feed.xml",$feed);
    }
    catch(\Exception $e){
      $this->logger->info($e->getMessage());
    }

    $this->logger->info('Google Feed Cron End');
 
  }
}