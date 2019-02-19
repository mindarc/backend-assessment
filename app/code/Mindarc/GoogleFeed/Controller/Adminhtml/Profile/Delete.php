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
 * Class Delete
 * @package Mindarc\GoogleFeed\Controller\Adminhtml\Profile
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mindarc_GoogleFeed::delete';

    /**
     * @var \Mindarc\GoogleFeed\Model\Profile
     */
    protected $profile;

    /**
     * Delete constructor.
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
        // check if we know what should be deleted
        $profileId = $this->getRequest()->getParam('id');
        if ($profileId) {
            try {
                // init model and delete
                $model = $this->profile->load($profileId);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('You deleted the profile.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while deleting profile data. Please try again.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
        } else {
            // display error message
            $this->messageManager->addError(__('We cannot find an profile to delete.'));

        }
        // go to grid
        $this->_redirect('google/profile/');
        return;
    }
}
