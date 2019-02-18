<?php
/**
 * Mindarc Pty Ltd.
 *
 * @category    Mindarc
 * @package     Mindarc_GoogleFeed
 * @author      Mindarc Team <hello@mindarc.com.au>
 * @copyright   Copyright (c) 2019 Mindarc Pty Ltd. (https://www.mindarc.com.au/)
 */

namespace Mindarc\GoogleFeed\Controller\Adminhtml\Profile;

/**
 * Class Generate
 * @package Mindarc\GoogleFeed\Controller\Adminhtml\Profile
 */
class Generate extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mindarc_GoogleFeed::generate';

    /**
     * @var \Mindarc\GoogleFeed\Model\FeedGenerator
     */
    private $feedGenerator;

    /**
     * Generate constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mindarc\GoogleFeed\Model\FeedGenerator $feedGenerator
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mindarc\GoogleFeed\Model\FeedGenerator $feedGenerator
    ) {
        parent::__construct($context);
        $this->feedGenerator = $feedGenerator;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $profileId = $this->getRequest()->getParam('id');
        if ($profileId) {
            $result = $this->feedGenerator->process($profileId);
        }

        // result is error (not an empty array of errors and not the product count of the profile)
        if (!empty($result) && !is_int($result)) {
            // display error message
            $this->messageManager->addError(
                sprintf(__('Error(s): %s'), implode(', ', $result))
            );
        } else {
            // display success message
            $this->messageManager->addSuccess(__('The profile has been generated.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        // go to preview
        return $resultRedirect->setPath('google/profile/');
    }
}
