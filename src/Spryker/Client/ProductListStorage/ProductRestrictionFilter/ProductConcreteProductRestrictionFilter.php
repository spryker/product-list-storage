<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\ProductRestrictionFilter;

use Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer;
use Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface;
use Spryker\Client\ProductListStorage\Exception\NotSupportedProductListTransferTypeException;
use Spryker\Client\ProductListStorage\ProductListProductConcreteStorage\ProductListProductConcreteStorageReaderInterface;

class ProductConcreteProductRestrictionFilter extends AbstractProductRestrictionFilter
{
    /**
     * @var \Spryker\Client\ProductListStorage\ProductListProductConcreteStorage\ProductListProductConcreteStorageReaderInterface
     */
    protected $productListProductConcreteStorageReader;

    /**
     * @param \Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface $customerClient
     * @param \Spryker\Client\ProductListStorage\ProductListProductConcreteStorage\ProductListProductConcreteStorageReaderInterface $productListProductConcreteStorageReader
     */
    public function __construct(ProductListStorageToCustomerClientInterface $customerClient, ProductListProductConcreteStorageReaderInterface $productListProductConcreteStorageReader)
    {
        parent::__construct($customerClient);
        $this->productListProductConcreteStorageReader = $productListProductConcreteStorageReader;
    }

    /**
     * @param int[] $productIds
     *
     * @return \Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer[]|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer[]
     */
    protected function getProductListStorageTransfers(array $productIds): array
    {
        return $this->productListProductConcreteStorageReader
            ->getProductConcreteProductListStorageTransfersByProductConcreteIds($productIds);
    }

    /**
     * @param mixed $productListStorageTransfer
     *
     * @throws \Spryker\Client\ProductListStorage\Exception\NotSupportedProductListTransferTypeException
     *
     * @return int
     */
    protected function getIdProduct($productListStorageTransfer): int
    {
        if (!$productListStorageTransfer instanceof ProductConcreteProductListStorageTransfer) {
            throw new NotSupportedProductListTransferTypeException();
        }
        /** @var \Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer $productListStorageTransfer */
        $productListStorageTransfer->requireIdProductConcrete();

        return $productListStorageTransfer->getIdProductConcrete();
    }
}
