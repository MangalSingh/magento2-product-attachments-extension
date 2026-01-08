<?php
namespace Octocub\ProductAttachments\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Octocub\ProductAttachments\Model\AttachmentFactory;
use Magento\Framework\Registry;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Octocub_ProductAttachments::attachments_manage';

    public function __construct(
        Action\Context $context,
        private PageFactory $pageFactory,
        private AttachmentFactory $attachmentFactory,
        private Registry $registry
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('attachment_id');
        $model = $this->attachmentFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This attachment no longer exists.'));
                return $this->_redirect('octocub_attachments/attachment/index');
            }
        }

        $this->registry->register('octocub_attachment', $model);

        $page = $this->pageFactory->create();
        $page->setActiveMenu('Octocub_ProductAttachments::attachments');
        $page->getConfig()->getTitle()->prepend($id ? __('Edit Attachment') : __('New Attachment'));
        return $page;
    }
}
