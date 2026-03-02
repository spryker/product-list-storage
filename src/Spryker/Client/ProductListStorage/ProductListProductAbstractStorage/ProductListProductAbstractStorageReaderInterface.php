<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\ProductListProductAbstractStorage;

use Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer;

interface ProductListProductAbstractStorageReaderInterface
{
    public function findProductAbstractProductListStorage(int $idProductAbstract): ?ProductAbstractProductListStorageTransfer;

    /**
     * @param array<int> $productAbstractIds
     *
     * @return array<\Generated\Shared\Transfer\ProductAbstractProductListStorageTransfer>
     */
    public function getProductAbstractProductListStorageTransfersByProductAbstractIds(array $productAbstractIds): array;
}
