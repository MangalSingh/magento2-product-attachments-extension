<?php
namespace Octocub\ProductAttachments\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class Link
{
    public function __construct(private ResourceConnection $rc) {}

    public function saveProductLinks(int $attachmentId, array $productIds): void
    {
        $conn = $this->rc->getConnection();
        $table = $this->rc->getTableName('octocub_product_attachment_product');

        $conn->delete($table, ['attachment_id = ?' => $attachmentId]);

        $pos = 0;
        foreach (array_values(array_unique(array_map('intval', $productIds))) as $pid) {
            if ($pid <= 0) continue;
            $conn->insert($table, [
                'attachment_id' => $attachmentId,
                'product_id' => $pid,
                'position' => $pos++,
            ]);
        }
    }

    public function getAttachmentIdsForProduct(int $productId): array
    {
        $conn = $this->rc->getConnection();
        $table = $this->rc->getTableName('octocub_product_attachment_product');
        return array_map('intval', $conn->fetchCol(
            "SELECT attachment_id FROM {$table} WHERE product_id = ? ORDER BY position ASC",
            [$productId]
        ));
    }
}
