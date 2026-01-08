<?php
namespace Octocub\ProductAttachments\Block\Product;

use Magento\Catalog\Block\Product\View as ProductView;
use Octocub\ProductAttachments\Model\Config;
use Octocub\ProductAttachments\Model\ResourceModel\Link as LinkResource;
use Octocub\ProductAttachments\Model\ResourceModel\Attachment\CollectionFactory;

class Attachments extends ProductView
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $stringUtils,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        private Config $config,
        private LinkResource $linkResource,
        private CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $stringUtils,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    public function canShow(): bool
    {
        return $this->config->enabled() && (bool)$this->getProduct()->getId();
    }

    public function getTabTitle(): string
    {
        return $this->config->tabTitle();
    }

    public function getHeading(): string
    {
        return $this->config->heading();
    }

    public function showFileSize(): bool
    {
        return $this->config->displayFileSize();
    }

    public function showDownloads(): bool
    {
        return $this->config->displayDownloads();
    }

    public function getAttachments(): array
    {
        $productId = (int)$this->getProduct()->getId();
        if (!$productId) return [];

        $ids = $this->linkResource->getAttachmentIdsForProduct($productId);
        if (!$ids) return [];

        $col = $this->collectionFactory->create();
        $col->addFieldToFilter('attachment_id', ['in' => $ids]);
        $col->addFieldToFilter('is_active', 1);

        $items = [];
        foreach ($col as $item) $items[(int)$item->getId()] = $item;

        $ordered = [];
        foreach ($ids as $id) {
            if (isset($items[$id])) $ordered[] = $items[$id];
        }
        return $ordered;
    }

    public function getDownloadUrl(int $attachmentId): string
    {
        return $this->getUrl('attachments/download/file', ['id' => $attachmentId, '_secure' => true]);
    }

    public function formatBytes(?int $bytes): string
    {
        if (!$bytes || $bytes <= 0) return '';
        $units = ['B','KB','MB','GB','TB'];
        $i = 0; $v = (float)$bytes;
        while ($v >= 1024 && $i < count($units)-1) { $v /= 1024; $i++; }
        return rtrim(rtrim(number_format($v, 2), '0'), '.') . ' ' . $units[$i];
    }
}
