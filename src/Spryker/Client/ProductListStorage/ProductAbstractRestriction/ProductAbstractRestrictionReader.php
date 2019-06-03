<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\ProductAbstractRestriction;

use Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer;
use Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface;
use Spryker\Client\ProductListStorage\ProductListProductAbstractStorage\ProductListProductAbstractStorageReaderInterface;

class ProductAbstractRestrictionReader implements ProductAbstractRestrictionReaderInterface
{
    /**
     * @var \Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface
     */
    protected $customerClient;

    /**
     * @var \Spryker\Client\ProductListStorage\ProductListProductAbstractStorage\ProductListProductAbstractStorageReaderInterface
     */
    protected $productListProductAbstractStorageReader;

    /**
     * @param \Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToCustomerClientInterface $customerClient
     * @param \Spryker\Client\ProductListStorage\ProductListProductAbstractStorage\ProductListProductAbstractStorageReaderInterface $productListProductAbstractStorageReader
     */
    public function __construct(
        ProductListStorageToCustomerClientInterface $customerClient,
        ProductListProductAbstractStorageReaderInterface $productListProductAbstractStorageReader
    ) {
        $this->customerClient = $customerClient;
        $this->productListProductAbstractStorageReader = $productListProductAbstractStorageReader;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return bool
     */
    public function isProductAbstractRestricted(int $idProductAbstract): bool
    {
        $customer = $this->customerClient->getCustomer();
        if (!$customer) {
            return false;
        }

        $customerProductListCollectionTransfer = $customer->getCustomerProductListCollection();
        if (!$customerProductListCollectionTransfer) {
            return false;
        }

        $customerWhitelistIds = $customer->getCustomerProductListCollection()->getWhitelistIds() ?: [];
        $customerBlacklistIds = $customer->getCustomerProductListCollection()->getBlacklistIds() ?: [];

        return $this->checkIfProductAbstractIsRestricted($idProductAbstract, $customerWhitelistIds, $customerBlacklistIds);
    }

    /**
     * @param int[] $productAbstractIds
     *
     * @return int[]
     */
    public function filterRestrictedAbstractProducts(array $productAbstractIds): array
    {
        $customer = $this->customerClient->getCustomer();
        if (!$customer) {
            return $productAbstractIds;
        }

        $customerProductListCollectionTransfer = $customer->getCustomerProductListCollection();
        if (!$customerProductListCollectionTransfer) {
            return $productAbstractIds;
        }

        $customerWhitelistIds = $customer->getCustomerProductListCollection()->getWhitelistIds() ?: [];
        $customerBlacklistIds = $customer->getCustomerProductListCollection()->getBlacklistIds() ?: [];

        if (!$customerBlacklistIds && !$customerWhitelistIds) {
            return $productAbstractIds;
        }

        $productListProductAbstractStorageTransfers = $this
            ->productListProductAbstractStorageReader
            ->findProductAbstractProductListStorageTransfersByProductAbstractIds($productAbstractIds);

        foreach ($productListProductAbstractStorageTransfers as $productListProductAbstractStorageTransfer) {
            if ($this->isProductAbstractRestrictedInBlacklist($productListProductAbstractStorageTransfer, $customerBlacklistIds)
                || $this->isProductAbstractRestrictedInWhitelist($productListProductAbstractStorageTransfer, $customerWhitelistIds)
            ) {
                $productAbstractIds = $this->removeIdProductAbstractFromList($productListProductAbstractStorageTransfer->getIdProductAbstract(), $productAbstractIds);
            }
        }

        return array_values($productAbstractIds);
    }

    /**
     * @param int $idProductAbstract
     * @param int[] $productAbstractIds
     *
     * @return int[]
     */
    protected function removeIdProductAbstractFromList(int $idProductAbstract, array $productAbstractIds): array
    {
        $key = array_search($idProductAbstract, $productAbstractIds);
        if ($key !== false) {
            unset($productAbstractIds[$key]);
        }

        return $productAbstractIds;
    }

    /**
     * @param int $idProductAbstract
     * @param int[] $customerWhitelistIds
     * @param int[] $customerBlacklistIds
     *
     * @return bool
     */
    protected function checkIfProductAbstractIsRestricted(
        int $idProductAbstract,
        array $customerWhitelistIds,
        array $customerBlacklistIds
    ): bool {
        if (!$customerBlacklistIds && !$customerWhitelistIds) {
            return false;
        }

        $productListProductAbstractStorageTransfer = $this->productListProductAbstractStorageReader->findProductAbstractProductListStorage($idProductAbstract);

        if ($productListProductAbstractStorageTransfer) {
            $isProductInBlacklist = count(array_intersect($productListProductAbstractStorageTransfer->getIdBlacklists(), $customerBlacklistIds));
            $isProductInWhitelist = count(array_intersect($productListProductAbstractStorageTransfer->getIdWhitelists(), $customerWhitelistIds));

            return $isProductInBlacklist || (count($customerWhitelistIds) && !$isProductInWhitelist);
        }

        return (bool)count($customerWhitelistIds);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer $productAbstractProductListStorageTransfer
     * @param int[] $customerWhitelistIds
     *
     * @return bool
     */
    protected function isProductAbstractRestrictedInWhitelist(
        ProductAbstractProductListStorageTransfer $productAbstractProductListStorageTransfer,
        array $customerWhitelistIds
    ): bool {
        if (empty($customerWhitelistIds)) {
            return false;
        }

        return empty(array_intersect($productAbstractProductListStorageTransfer->getIdWhitelists(), $customerWhitelistIds));
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer $productAbstractProductListStorageTransfer
     * @param int[] $customerBlacklistIds
     *
     * @return bool
     */
    protected function isProductAbstractRestrictedInBlacklist(
        ProductAbstractProductListStorageTransfer $productAbstractProductListStorageTransfer,
        array $customerBlacklistIds
    ): bool {
        if (empty($customerBlacklistIds)) {
            return false;
        }

        return !empty(array_intersect($productAbstractProductListStorageTransfer->getIdBlacklists(), $customerBlacklistIds));
    }
}
