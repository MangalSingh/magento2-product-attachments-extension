<?php
namespace Octocub\ProductAttachments\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Octocub_ProductAttachments::attachments_manage';

    public function __construct(Action\Context $context, private PageFactory $pageFactory)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Octocub_ProductAttachments::attachments');
        $page->getConfig()->getTitle()->prepend(__('Product Attachments'));
        return $page;
    }
}
