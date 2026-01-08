<?php
namespace Octocub\ProductAttachments\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action;
use Octocub\ProductAttachments\Model\AttachmentFactory;
use Octocub\ProductAttachments\Model\ResourceModel\Link as LinkResource;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Octocub_ProductAttachments::attachments_manage';

    public function __construct(
        Action\Context $context,
        private AttachmentFactory $attachmentFactory,
        private LinkResource $linkResource,
        private UploaderFactory $uploaderFactory,
        private Filesystem $filesystem
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $data = (array)$this->getRequest()->getPostValue();
        if (!$data) {
            return $this->_redirect('octocub_attachments/attachment/index');
        }

        $id = (int)($data['attachment_id'] ?? 0);
        $model = $this->attachmentFactory->create();
        if ($id) $model->load($id);

        try {
            $model->setData('title', (string)($data['title'] ?? ''));
            $model->setData('is_active', isset($data['is_active']) ? (int)$data['is_active'] : 1);

            if (!empty($_FILES['file']['name'])) {
                $uploader = $this->uploaderFactory->create(['fileId' => 'file']);
                $uploader->setAllowedExtensions(['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','jpg','jpeg','png','gif','txt']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $mediaDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                $target = 'product_attachments';
                $mediaDir->create($target);

                $result = $uploader->save($mediaDir->getAbsolutePath($target));
                $relPath = $target . '/' . ltrim($result['file'], '/');

                $model->setData('file_name', (string)($result['name'] ?? basename($relPath)));
                $model->setData('file_path', $relPath);
                $abs = $mediaDir->getAbsolutePath($relPath);
                $model->setData('file_size', is_file($abs) ? filesize($abs) : null);
            } elseif (!$model->getId()) {
                throw new \RuntimeException('Please upload a file.');
            }

            $model->save();

            $productIds = [];
            if (!empty($data['product_ids'])) {
                $productIds = preg_split('/\s*,\s*/', (string)$data['product_ids']);
            }
            $this->linkResource->saveProductLinks((int)$model->getId(), $productIds);

            $this->messageManager->addSuccessMessage(__('Attachment saved.'));
            if ($this->getRequest()->getParam('back')) {
                return $this->_redirect('octocub_attachments/attachment/edit', ['attachment_id' => $model->getId()]);
            }
            return $this->_redirect('octocub_attachments/attachment/index');
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage(__('Error: %1', $e->getMessage()));
            if ($id) {
                return $this->_redirect('octocub_attachments/attachment/edit', ['attachment_id' => $id]);
            }
            return $this->_redirect('octocub_attachments/attachment/new');
        }
    }
}
