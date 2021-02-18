<?php

namespace Mastering\SampleModule\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductSave implements ObserverInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * ProductSave constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resource = $resourceConnection;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        $sampleData = $product->getName() . ' - ' . $product->getSku();
        $product->setCustomAttribute('sample_attribute', $sampleData);

    }
}
