<?php
namespace Octocub\ProductAttachments\Model\ResourceModel\Attachment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Octocub\ProductAttachments\Model\Attachment::class,
            \Octocub\ProductAttachments\Model\ResourceModel\Attachment::class
        );
    }
}
