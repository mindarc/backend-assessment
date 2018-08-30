<?php
namespace Kai\Googlefeed\Controller\Adminhtml\Index;

/**
 * Class Index
 *
 * Handle URL routing in the backend
 *
 * @author Cheng Shea kai <gabazoo@gmail.com>
 * @see https://github.com/bugcskai/
 */
class Index extends \Magento\Backend\App\Action
{
   
    private $_feed;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Kai\Googlefeed\Model\Feed $feed
    ) {
        $this->_feed = $feed;
        parent::__construct($context);
    }
    
    public function execute()
    {
        header('Content-Type: application/xml; charset=utf-8');
        echo $this->_feed->generateFeed();

    }
}