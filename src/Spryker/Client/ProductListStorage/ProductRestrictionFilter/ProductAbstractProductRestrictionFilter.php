<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\ProductRestrictionFilter;

use Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface;
use Spryker\Client\ProductListStorage\ProductListProductAbstractStorage\ProductListProductAbstractStorageReaderInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class ProductAbstractProductRestrictionFilter extends AbstractProductRestrictionFilter
{
    /**
     * @var \Spryker\Client\ProductListStorage\ProductListProductAbstractStorage\ProductListProductAbstractStorageReaderInterface
     */
    protected $productListProductAbstractStorageReader;

    /**
     * @param \Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface $customerClient
     * @param \Spryker\Client\ProductListStorage\ProductListProductAbstractStorage\ProductListProductAbstractStorageReaderInterface $productListProductAbstractStorageReader
     */
    public function __construct(ProductListStorageToCustomerClientInterface $customerClient, ProductListProductAbstractStorageReaderInterface $productListProductAbstractStorageReader)
    {
        parent::__construct($customerClient);
        $this->productListProductAbstractStorageReader = $productListProductAbstractStorageReader;
    }

    /**
     * @param int[] $productIds
     *
     * @return \Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer[]|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer[]
     */
    protected function getProductListStorageTransfers(array $productIds): array
    {
        return $this
            ->productListProductAbstractStorageReader
            ->getProductAbstractProductListStorageTransfersByProductAbstractIds($productIds);
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer $productListStorageTransfer
     *
     * @return int
     */
    protected function getIdProduct(AbstractTransfer $productListStorageTransfer): int
    {
        $productListStorageTransfer->requireIdProductAbstract();

        return $productListStorageTransfer->getIdProductAbstract();
    }
}
