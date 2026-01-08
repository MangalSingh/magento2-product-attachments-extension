<?php
namespace Octocub\ProductAttachments\Ui\DataProvider\Attachment;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Octocub\ProductAttachments\Model\ResourceModel\Attachment\CollectionFactory;

class ListingDataProvider extends AbstractDataProvider
{
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        private CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $this->collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
}
