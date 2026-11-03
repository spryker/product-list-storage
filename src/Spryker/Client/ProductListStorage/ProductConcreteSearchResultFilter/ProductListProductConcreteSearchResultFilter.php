<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\ProductConcreteSearchResultFilter;

use Generated\Shared\Transfer\ProductConcreteCriteriaFilterTransfer;
use Generated\Shared\Transfer\ProductConcretePageSearchTransfer;
use Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer;
use Generated\Shared\Transfer\ProductListTransfer;
use Spryker\Client\ProductListStorage\ProductListProductConcreteStorage\ProductListProductConcreteStorageReaderInterface;

class ProductListProductConcreteSearchResultFilter implements ProductListProductConcreteSearchResultFilterInterface
{
    /**
     * @uses \Spryker\Client\ProductListSearch\Plugin\Search\ProductListQueryExpanderPlugin::REQUEST_PARAM_ID_PRODUCT_LIST
     */
    protected const string REQUEST_PARAM_ID_PRODUCT_LIST = ProductListTransfer::ID_PRODUCT_LIST;

    public function __construct(
        protected ProductListProductConcreteStorageReaderInterface $productListProductConcreteStorageReader
    ) {
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductConcretePageSearchTransfer> $productConcretePageSearchTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductConcretePageSearchTransfer>
     */
    public function filterByProductList(
        array $productConcretePageSearchTransfers,
        ProductConcreteCriteriaFilterTransfer $productConcreteCriteriaFilterTransfer
    ): array {
        $idProductList = $productConcreteCriteriaFilterTransfer->getRequestParams()[static::REQUEST_PARAM_ID_PRODUCT_LIST] ?? null;

        if ($idProductList === null || !$productConcretePageSearchTransfers) {
            return $productConcretePageSearchTransfers;
        }

        $productConcreteProductListStorageTransfers = $this->getProductConcreteProductListStorageTransfersIndexedByIdProductConcrete(
            $this->extractProductConcreteIds($productConcretePageSearchTransfers),
        );

        return array_values(array_filter(
            $productConcretePageSearchTransfers,
            fn (ProductConcretePageSearchTransfer $productConcretePageSearchTransfer): bool => $this->isInProductList(
                $productConcreteProductListStorageTransfers[$productConcretePageSearchTransfer->getFkProductOrFail()] ?? null,
                (int)$idProductList,
            ),
        ));
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductConcretePageSearchTransfer> $productConcretePageSearchTransfers
     *
     * @return array<int>
     */
    protected function extractProductConcreteIds(array $productConcretePageSearchTransfers): array
    {
        $productConcreteIds = array_map(
            static fn (ProductConcretePageSearchTransfer $productConcretePageSearchTransfer): int => $productConcretePageSearchTransfer->getFkProductOrFail(),
            $productConcretePageSearchTransfers,
        );

        return array_values(array_unique($productConcreteIds));
    }

    /**
     * @param array<int> $productConcreteIds
     *
     * @return array<int, \Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer>
     */
    protected function getProductConcreteProductListStorageTransfersIndexedByIdProductConcrete(array $productConcreteIds): array
    {
        $productConcreteProductListStorageTransfers = $this->productListProductConcreteStorageReader
            ->getProductConcreteProductListStorageTransfersByProductConcreteIds($productConcreteIds);

        $indexedProductConcreteProductListStorageTransfers = [];

        foreach ($productConcreteProductListStorageTransfers as $productConcreteProductListStorageTransfer) {
            $indexedProductConcreteProductListStorageTransfers[(int)$productConcreteProductListStorageTransfer->getIdProductConcrete()] = $productConcreteProductListStorageTransfer;
        }

        return $indexedProductConcreteProductListStorageTransfers;
    }

    protected function isInProductList(
        ?ProductConcreteProductListStorageTransfer $productConcreteProductListStorageTransfer,
        int $idProductList
    ): bool {
        if ($productConcreteProductListStorageTransfer === null) {
            return false;
        }

        $productConcreteOwnProductListsStorageTransfer = $productConcreteProductListStorageTransfer->getOwnProductLists();

        if ($productConcreteOwnProductListsStorageTransfer === null) {
            return true;
        }

        $productListIds = array_map('intval', array_merge(
            $productConcreteOwnProductListsStorageTransfer->getWhitelistIds(),
            $productConcreteOwnProductListsStorageTransfer->getBlacklistIds(),
        ));

        return in_array($idProductList, $productListIds, true);
    }
}
