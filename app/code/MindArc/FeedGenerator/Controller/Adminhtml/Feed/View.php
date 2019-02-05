<?php

namespace MindArc\FeedGenerator\Controller\Adminhtml\Feed;

class View
    extends \Magento\Backend\App\Action
{
    const XML_TMP_PATH = 'Feeds/';

    protected $feed;

    protected $messageManager;

    protected $feedHelper;

    public function __construct(
        \MindArc\FeedGenerator\Helper\Data $feedHelper,
        \Magento\Framework\Filesystem $filesystem,
        \MindArc\FeedGenerator\Model\Feed $feed,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->feedHelper = $feedHelper;
        $this->feed = $feed;
        $this->messageManager = $context->getMessageManager();
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererUrl();

        $feedGenerated = $this->feed->generateFeed();

        $this->showFeedContents($feedGenerated);

        if ($feedGenerated) {
            $this->messageManager->addNoticeMessage('Feed has been generated successful');
        } else {
            $this->messageManager->addErrorMessage('Error to generated the feed');
        }

        return $resultRedirect;
    }

    protected function showFeedContents($feed)
    {
        echo $feed;
    }


}