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
 * Class Edit
 * @package Mindarc\GoogleFeed\Controller\Adminhtml\Profile
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mindarc_GoogleFeed::form';

    /**
     * @var \Mindarc\GoogleFeed\Model\Profile
     */
    protected $profile;

    /**
     * Edit constructor.
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

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Mindarc_GoogleFeed::profiles');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Profiles'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? 'Edit - ' . $model->getProfileName() : __('New Profile')
        );

        $this->_addBreadcrumb(
            $profileId ? __('Edit Profile') : __('New Profile'),
            $profileId ? __('Edit Profile') : __('New Profile')
        );
        $this->_view->renderLayout();
    }
}
