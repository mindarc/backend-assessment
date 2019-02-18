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
 * Class Save
 * @package Mindarc\GoogleFeed\Controller\Adminhtml\Profile
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mindarc_GoogleFeed::save';

    /**
     * @var \Mindarc\GoogleFeed\Model\Profile
     */
    protected $profile;

    /**
     * Save constructor.
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
     * Create new master data
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('adminhtml/*/');
        }

        /** @var \Mindarc\GoogleFeed\Model\Profile $model */
        $model = $this->profile->initProfile((int)$this->getRequest()->getParam('entity_id'));
        if (!$this->isProfileExist($model)) {
            $this->messageManager->addError(__('This profile does not exist.'));
            return $resultRedirect->setPath('adminhtml/*/');
        }

        try {
            if (!empty($data)) {
                $model->addData($data);
                $this->_getSession()->setFormData($data);
            }
            $model->save();
            $this->_getSession()->setFormData(false);
            $this->messageManager->addSuccess(__('You saved the profile.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $redirectBack = true;
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $redirectBack = true;
            $this->messageManager->addError(__('We cannot save the profile.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }

        return ($redirectBack)
            ? $resultRedirect->setPath('google/profile/edit', ['id' => $model->getId(), '_current' => true])
            : $resultRedirect->setPath('google/profile/');
    }

    /**
     * Check if profile exist
     *
     * @param \Mindarc\GoogleFeed\Model\Profile $model
     * @return bool
     */
    protected function isProfileExist(\Mindarc\GoogleFeed\Model\Profile $model)
    {
        $entityId = $this->getRequest()->getParam('id');
        return (!$model->getId() && $entityId) ? false : true;
    }
}
