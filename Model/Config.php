<?php
namespace Octocub\ProductAttachments\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public function __construct(private ScopeConfigInterface $scopeConfig) {}

    private function get(string $path): string
    {
        return (string)$this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    public function enabled(): bool { return $this->get('octocub_product_attachments/general/enabled') === '1'; }
    public function tabTitle(): string { return $this->get('octocub_product_attachments/general/tab_title') ?: 'Attachments'; }
    public function heading(): string { return $this->get('octocub_product_attachments/general/heading') ?: 'Product Attachments'; }
    public function displayFileSize(): bool { return $this->get('octocub_product_attachments/general/display_file_size') === '1'; }
    public function displayDownloads(): bool { return $this->get('octocub_product_attachments/general/display_downloads') === '1'; }
}
