<?php
namespace Octocub\ProductAttachments\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class CreateTables implements SchemaPatchInterface
{
    public function __construct(private SchemaSetupInterface $schemaSetup) {}

    public function apply()
    {
        $setup = $this->schemaSetup;
        $setup->startSetup();
        $conn = $setup->getConnection();

        $attachmentTable = $setup->getTable('octocub_product_attachment');
        if (!$conn->isTableExists($attachmentTable)) {
            $table = $conn->newTable($attachmentTable)
                ->addColumn('attachment_id', Table::TYPE_INTEGER, null, [
                    'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true
                ], 'Attachment ID')
                ->addColumn('title', Table::TYPE_TEXT, 255, ['nullable' => false], 'Title')
                ->addColumn('file_name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Original File Name')
                ->addColumn('file_path', Table::TYPE_TEXT, 1024, ['nullable' => false], 'Relative Media Path')
                ->addColumn('file_size', Table::TYPE_BIGINT, null, ['nullable' => true], 'File Size Bytes')
                ->addColumn('downloads', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0], 'Downloads')
                ->addColumn('is_active', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => 1], 'Is Active')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE], 'Updated')
                ->addIndex($setup->getIdxName($attachmentTable, ['is_active']), ['is_active'])
                ->setComment('Octocub Product Attachments');
            $conn->createTable($table);
        }

        $linkTable = $setup->getTable('octocub_product_attachment_product');
        if (!$conn->isTableExists($linkTable)) {
            $table = $conn->newTable($linkTable)
                ->addColumn('link_id', Table::TYPE_INTEGER, null, [
                    'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true
                ], 'Link ID')
                ->addColumn('attachment_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Attachment ID')
                ->addColumn('product_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'Product ID')
                ->addColumn('position', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => 0], 'Position')
                ->addIndex($setup->getIdxName($linkTable, ['attachment_id']), ['attachment_id'])
                ->addIndex($setup->getIdxName($linkTable, ['product_id']), ['product_id'])
                ->addForeignKey(
                    $setup->getFkName($linkTable, 'attachment_id', $attachmentTable, 'attachment_id'),
                    'attachment_id',
                    $attachmentTable,
                    'attachment_id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $setup->getFkName($linkTable, 'product_id', $setup->getTable('catalog_product_entity'), 'entity_id'),
                    'product_id',
                    $setup->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Octocub Attachment to Product Link');
            $conn->createTable($table);
        }

        $setup->endSetup();
    }

    public static function getDependencies(): array { return []; }
    public function getAliases(): array { return []; }
}
