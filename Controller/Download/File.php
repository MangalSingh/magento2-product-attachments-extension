<?php
namespace Octocub\ProductAttachments\Controller\Download;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Octocub\ProductAttachments\Model\AttachmentFactory;

class File extends Action
{
    public function __construct(
        Context $context,
        private AttachmentFactory $attachmentFactory,
        private Filesystem $filesystem,
        private RawFactory $rawFactory
    ) { parent::__construct($context); }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if (!$id) return $this->noroute();

        $att = $this->attachmentFactory->create()->load($id);
        if (!$att->getId() || (int)$att->getData('is_active') !== 1) return $this->noroute();

        $media = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $rel = (string)$att->getData('file_path');
        $abs = $media->getAbsolutePath($rel);

        if (!is_file($abs)) return $this->noroute();

        try {
            $att->setData('downloads', ((int)$att->getData('downloads')) + 1);
            $att->save();
        } catch (\Throwable $e) {}

        $result = $this->rawFactory->create();
        $result->setHeader('Content-Type', function_exists('mime_content_type') ? (mime_content_type($abs) ?: 'application/octet-stream') : 'application/octet-stream', true);
        $result->setHeader('Content-Disposition', 'attachment; filename="' . basename((string)($att->getData('file_name') ?: $abs)) . '"', true);
        $result->setHeader('Content-Length', (string)filesize($abs), true);
        $result->setContents(file_get_contents($abs));
        return $result;
    }

    private function noroute()
    {
        $this->_forward('noroute');
        return $this->rawFactory->create();
    }
}
