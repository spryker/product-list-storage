<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\ProductRestrictionFilter;

use Generated\Shared\Transfer\CustomerProductListCollectionTransfer;
use Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

abstract class AbstractProductRestrictionFilter implements ProductRestrictionFilterInterface
{
    /**
     * @var \Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface
     */
    protected $customerClient;

    /**
     * @param \Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface $customerClient
     */
    public function __construct(ProductListStorageToCustomerClientInterface $customerClient)
    {
        $this->customerClient = $customerClient;
    }

    /**
     * @param int[] $productIds
     *
     * @return int[]
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

        return $this->filterProductIds($productIds, $productListStorageTransfers, $customerWhitelistIds, $customerBlacklistIds);
    }

    /**
     * @param int[] $productIds
     * @param \Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer[]|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer[] $productListStorageTransfers
     * @param int[] $customerWhitelistIds
     * @param int[] $customerBlacklistIds
     *
     * @return int[]
     */
    protected function filterProductIds(array $productIds, array $productListStorageTransfers, array $customerWhitelistIds, array $customerBlacklistIds): array
    {
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
        $customerTransfer = $this->customerClient->getCustomer();
        if (!$customerTransfer) {
            return null;
        }

        return $customerTransfer->getCustomerProductListCollection();
    }

    /**
     * @param int $idProduct
     * @param int[] $productIds
     *
     * @return int[]
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
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer $productListStorageTransfer
     * @param array $customerBlackListIds
     * @param array $customerWhiteListIds
     *
     * @return bool
     */
    protected function isProductRestricted(
        AbstractTransfer $productListStorageTransfer,
        array $customerBlackListIds,
        array $customerWhiteListIds
    ): bool {
        $isProductInBlacklist = count(array_intersect($productListStorageTransfer->getIdBlacklists(), $customerBlackListIds));
        $isProductInWhitelist = count(array_intersect($productListStorageTransfer->getIdWhitelists(), $customerWhiteListIds));

        return $isProductInBlacklist || (count($customerWhiteListIds) && !$isProductInWhitelist);
    }

    /**
     * @param int[] $productIds
     *
     * @return \Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer[]|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer[]
     */
    abstract protected function getProductListStorageTransfers(array $productIds): array;

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer|\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer $productListStorageTransfer
     *
     * @return int
     */
    abstract protected function getIdProduct(AbstractTransfer $productListStorageTransfer): int;
}
