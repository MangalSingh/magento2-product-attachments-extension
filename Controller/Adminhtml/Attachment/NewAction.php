<?php
namespace Octocub\ProductAttachments\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action;

class NewAction extends Action
{
    const ADMIN_RESOURCE = 'Octocub_ProductAttachments::attachments_manage';

    public function execute()
    {
        return $this->_redirect('octocub_attachments/attachment/edit');
    }
}
