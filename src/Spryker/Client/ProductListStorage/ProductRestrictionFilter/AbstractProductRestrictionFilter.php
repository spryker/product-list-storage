<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\ProductRestrictionFilter;

use Generated\Shared\Transfer\CustomerProductListCollectionTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer;
use Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer;
use Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface;
use Spryker\Client\ProductListStorage\Exception\NotSupportedProductListTransferTypeException;

abstract class AbstractProductRestrictionFilter implements ProductRestrictionFilterInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE_NOT_SUPPORTED_PRODUCT_LIST_TRANSFER_TYPE = 'Type `%s` is not supported for product filtering. Please use `%s` instead.';

    /**
     * @var \Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface
     */
    protected $customerClient;

    /**
     * @var bool
     */
    protected static $customerRequested = false;

    /**
     * @var \Generated\Shared\Transfer\CustomerTransfer|null
     */
    protected static $customerTransfer;

    /**
     * @param \Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface $customerClient
     */
    public function __construct(ProductListStorageToCustomerClientInterface $customerClient)
    {
        $this->customerClient = $customerClient;
    }

    /**
     * @param array<int> $productIds
     *
     * @return array<int>
     */
    public function filterRestrictedProducts(array $productIds): array
    {
        $customerProductListCollectionTransfer = $this->findCustomerProductListCollectionTransfer();
        if (!$customerProductListCollectionTransfer) {
            return $productIds;
        }

        $customerWhitelistIds = $customerProductListCollectionTransfer->getWhitelistIds() ?: [];
        $customerBlacklistIds = $customerProductListCollectionTransfer->getBlacklistIds() ?: [];

        $productListStorageTransfers = $this->getProductListStorageTransfers($productIds);

        return $this->filterProductIdsByCustomerProductLists($productIds, $productListStorageTransfers, $customerWhitelistIds, $customerBlacklistIds);
    }

    /**
     * @param array<int> $productIds
     * @param array<\Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer> $productListStorageTransfers
     * @param array<int> $customerWhitelistIds
     * @param array<int> $customerBlacklistIds
     *
     * @return array<int>
     */
    protected function filterProductIdsByCustomerProductLists(
        array $productIds,
        array $productListStorageTransfers,
        array $customerWhitelistIds,
        array $customerBlacklistIds
    ): array {
        if (!$productListStorageTransfers || (!$customerBlacklistIds && !$customerWhitelistIds)) {
            return $productIds;
        }

        foreach ($productListStorageTransfers as $productListStorageTransfer) {
            if ($this->isProductRestricted($productListStorageTransfer, $customerBlacklistIds, $customerWhitelistIds)) {
                $productIds = $this->removeIdProductFromList($this->getIdProduct($productListStorageTransfer), $productIds);
            }
        }

        return array_values($productIds);
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerProductListCollectionTransfer|null
     */
    protected function findCustomerProductListCollectionTransfer(): ?CustomerProductListCollectionTransfer
    {
        $customerTransfer = $this->getCustomerTransfer();
        if (!$customerTransfer) {
            return null;
        }

        return $customerTransfer->getCustomerProductListCollection();
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    protected function getCustomerTransfer(): ?CustomerTransfer
    {
        if (!static::$customerRequested) {
            static::$customerRequested = true;
            static::$customerTransfer = $this->customerClient->getCustomer();
        }

        return static::$customerTransfer;
    }

    /**
     * @param int $idProduct
     * @param array<int> $productIds
     *
     * @return array<int>
     */
    protected function removeIdProductFromList(int $idProduct, array $productIds): array
    {
        $key = array_search($idProduct, $productIds);
        if ($key !== false) {
            unset($productIds[$key]);
        }

        return $productIds;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer $productListStorageTransfer
     * @param array $customerBlacklistIds
     * @param array $customerWhitelistIds
     *
     * @return bool
     */
    protected function isProductRestricted(
        $productListStorageTransfer,
        array $customerBlacklistIds,
        array $customerWhitelistIds
    ): bool {
        $this->assertProductListTransferRequiredType($productListStorageTransfer);

        $isProductInBlacklist = count(array_intersect($productListStorageTransfer->getIdBlacklists(), $customerBlacklistIds));
        $isProductInWhitelist = count(array_intersect($productListStorageTransfer->getIdWhitelists(), $customerWhitelistIds));

        return $isProductInBlacklist || (count($customerWhitelistIds) && !$isProductInWhitelist);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer|mixed $productListStorageTransfer
     *
     * @throws \Spryker\Client\ProductListStorage\Exception\NotSupportedProductListTransferTypeException
     *
     * @return void
     */
    protected function assertProductListTransferRequiredType($productListStorageTransfer): void
    {
        if (
            !$productListStorageTransfer instanceof ProductAbstractProductListStorageTransfer
            && !$productListStorageTransfer instanceof ProductConcreteProductListStorageTransfer
        ) {
            $expectedType = implode(' or ', [ProductAbstractProductListStorageTransfer::class, ProductConcreteProductListStorageTransfer::class]);
            $actualType = is_object($productListStorageTransfer) ? get_class($productListStorageTransfer) : gettype($productListStorageTransfer);

            throw new NotSupportedProductListTransferTypeException(sprintf(static::ERROR_MESSAGE_NOT_SUPPORTED_PRODUCT_LIST_TRANSFER_TYPE, $actualType, $expectedType));
        }
    }

    /**
     * @param array<int> $productIds
     *
     * @return array<\Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer>
     */
    abstract protected function getProductListStorageTransfers(array $productIds): array;

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer|mixed $productListStorageTransfer
     *
     * @return int
     */
    abstract protected function getIdProduct($productListStorageTransfer): int;
}
