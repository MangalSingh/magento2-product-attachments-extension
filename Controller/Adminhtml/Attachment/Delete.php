<?php
namespace Octocub\ProductAttachments\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action;
use Octocub\ProductAttachments\Model\AttachmentFactory;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Octocub_ProductAttachments::attachments_manage';

    public function __construct(Action\Context $context, private AttachmentFactory $attachmentFactory)
    { parent::__construct($context); }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('attachment_id');
        if (!$id) return $this->_redirect('octocub_attachments/attachment/index');

        try {
            $model = $this->attachmentFactory->create()->load($id);
            if ($model->getId()) $model->delete();
            $this->messageManager->addSuccessMessage(__('Attachment deleted.'));
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage(__('Error: %1', $e->getMessage()));
        }

        return $this->_redirect('octocub_attachments/attachment/index');
    }
}
