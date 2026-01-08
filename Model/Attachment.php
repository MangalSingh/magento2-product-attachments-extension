<?php
namespace Octocub\ProductAttachments\Model;

use Magento\Framework\Model\AbstractModel;

class Attachment extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Octocub\ProductAttachments\Model\ResourceModel\Attachment::class);
    }
}
