<?php
namespace Kai\Googlefeed\Controller\feed;

/**
 * Class Index
 *
 * Handle URL routing in the frontend
 * Usage <site_url>/kaigooglefeed/feed
 *
 * @author Cheng Shea kai <gabazoo@gmail.com>
 * @see https://github.com/bugcskai/
 */
class Index extends \Magento\Framework\App\Action\Action
{
    
    private $feed;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Kai\Googlefeed\Model\Feed $feed
    ) {
        $this->feed = $feed;
        parent::__construct($context);
    }

    public function execute()
    {
        header('Content-Type: application/xml; charset=utf-8');
        echo $this->feed->generateFeed();
    }
}
