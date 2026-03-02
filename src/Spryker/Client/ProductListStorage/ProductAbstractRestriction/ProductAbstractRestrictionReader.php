<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\ProductAbstractRestriction;

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

    public function __construct(
        ProductListStorageToCustomerClientInterface $customerClient,
        ProductListProductAbstractStorageReaderInterface $productListProductAbstractStorageReader
    ) {
        $this->customerClient = $customerClient;
        $this->productListProductAbstractStorageReader = $productListProductAbstractStorageReader;
    }

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
     * @param int $idProductAbstract
     * @param array<int> $customerWhitelistIds
     * @param array<int> $customerBlacklistIds
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
}
