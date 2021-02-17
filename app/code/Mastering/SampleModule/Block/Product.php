<?php

namespace Mastering\SampleModule\Block;

use Magento\Catalog\Helper\Data;
use Magento\Catalog\Model\ProductFactory as CatalogProduct;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class Product extends Template
{
    /**
     * @var Data
     */
    private $productHelper;
    /**
     * @var Resolver
     */
    private $locale;

    /**
     * @var CatalogProduct
     */
    public $product;

    /**
     * @param Context  $context
     * @param Resolver $locale
     * @param Data     $productHelper
     * @param array    $data
     * @codeCoverageIgnore
     */
    public function __construct(CatalogProduct $product, Context $context, Resolver $locale, Data $productHelper, array $data = [])
    {
        parent::__construct($context, $data);
        $this->product = $product;
        $this->locale        = $locale;
        $this->productHelper = $productHelper;
    }

    /**
     * Check to see if display on product is enabled or not
     *
     * @return bool
     */
    public function showOnProduct(): bool
    {
        return $this->isSetFlag('klarna/osm/enabled')
            && $this->isSetFlag('klarna/osm/product_enabled');
    }

    /**
     * @return mixed|null
     */
    public function getSampleData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Framework\Registry')->registry('current_product');
        return $this->product->create()->load($product->getId())->getDataByKey('sample_attribute');
    }

    /**
     * Get the locale according to ISO_3166-1 standard
     *
     * @return string
     */
    public function getLocale(): string
    {
        return str_replace('_', '-', $this->locale->getLocale());
    }

    /**
     * Get placement id
     *
     * @return string
     */
    public function getPlacementId(): string
    {
        $placementId = $this->getValue('klarna/osm/product_placement_select');
        if ($placementId && $placementId === 'other') {
            $placementId = $this->getValue('klarna/osm/product_placement_other');
        }
        return $placementId;
    }

    /**
     * Get theme (default or dark)
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->getValue('klarna/osm/theme');
    }

    /**
     * Get the amount of the purchase formated as an integer `round(amount * 100)`
     *
     * @return int
     */
    public function getPurchaseAmount(): int
    {
        $product = $this->productHelper->getProduct();
        $price   = $product->getFinalPrice($product->getQty());
        return (int)round($price * 100);
    }

    /**
     * Wrapper around `$this->_scopeConfig->isSetFlag` that ensures store scope is checked
     *
     * @param string $path
     * @return bool
     */
    private function isSetFlag(string $path): bool
    {
        return $this->_scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore());
    }

    /**
     * Wrapper around `$this->_scopeConfig->getValue` that ensures store scope is checked
     *
     * @param string $path
     * @return mixed
     */
    private function getValue(string $path)
    {
        return $this->_scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore());
    }
}
