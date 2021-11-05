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
     * @var string
     */
    protected const ERROR_MESSAGE_WRONG_PRODUCT_LIST_TRANSFER_TYPE = 'Type `%s` is not supported for getting concrete product id. Please use `%s` instead.';

    /**
     * @var \Spryker\Client\ProductListStorage\ProductListProductConcreteStorage\ProductListProductConcreteStorageReaderInterface
     */
    protected $productListProductConcreteStorageReader;

    /**
     * @param \Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface $customerClient
     * @param \Spryker\Client\ProductListStorage\ProductListProductConcreteStorage\ProductListProductConcreteStorageReaderInterface $productListProductConcreteStorageReader
     */
    public function __construct(
        ProductListStorageToCustomerClientInterface $customerClient,
        ProductListProductConcreteStorageReaderInterface $productListProductConcreteStorageReader
    ) {
        parent::__construct($customerClient);
        $this->productListProductConcreteStorageReader = $productListProductConcreteStorageReader;
    }

    /**
     * @param array<int> $productIds
     *
     * @return array<\Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer>
     */
    protected function getProductListStorageTransfers(array $productIds): array
    {
        return $this->productListProductConcreteStorageReader
            ->getProductConcreteProductListStorageTransfersByProductConcreteIds($productIds);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer|mixed $productListStorageTransfer
     *
     * @throws \Spryker\Client\ProductListStorage\Exception\NotSupportedProductListTransferTypeException
     *
     * @return int
     */
    protected function getIdProduct($productListStorageTransfer): int
    {
        if (!$productListStorageTransfer instanceof ProductConcreteProductListStorageTransfer) {
            $actualType = is_object($productListStorageTransfer) ? get_class($productListStorageTransfer) : gettype($productListStorageTransfer);

            throw new NotSupportedProductListTransferTypeException(sprintf(static::ERROR_MESSAGE_WRONG_PRODUCT_LIST_TRANSFER_TYPE, $actualType, ProductConcreteProductListStorageTransfer::class));
        }
        $productListStorageTransfer->requireIdProductConcrete();

        return $productListStorageTransfer->getIdProductConcrete();
    }
}
