<?php
namespace Octocub\ProductAttachments\Block\Adminhtml\Attachment;

use Magento\Backend\Block\Template;
use Magento\Framework\Registry;

class Edit extends Template
{
    public function __construct(Template\Context $context, private Registry $registry, array $data = [])
    { parent::__construct($context, $data); }

    public function getModel()
    {
        return $this->registry->registry('octocub_attachment');
    }

    public function getSaveUrl(): string
    {
        return $this->getUrl('octocub_attachments/attachment/save');
    }

    public function getBackUrl(): string
    {
        return $this->getUrl('octocub_attachments/attachment/index');
    }
}
