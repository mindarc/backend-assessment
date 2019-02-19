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
 * Class Preview
 * @package Mindarc\GoogleFeed\Controller\Adminhtml\Profile
 */
class Preview extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mindarc_GoogleFeed::profiles';

    /**
     * @var \Mindarc\GoogleFeed\Model\Profile
     */
    protected $profile;

    /**
     * Preview constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mindarc\GoogleFeed\Model\Profile $profile
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mindarc\GoogleFeed\Model\Profile $profile
    ) {
        parent::__construct($context);
        $this->profile = $profile;
    }

    /**
     * Edit master data
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $profileId = $this->getRequest()->getParam('id');
        $model = $this->profile->initProfile($profileId);

        if (!$model->getId() && $profileId) {
            $this->messageManager->addError(__('This profile no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Mindarc_GoogleFeed::profiles');

        $pageTitle = __('Preview Feed - %1', $model->getProfileName());

        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Profile'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend($pageTitle);

        $this->_addBreadcrumb(
            $pageTitle,
            $pageTitle
        );
        $this->_view->renderLayout();
    }
}
