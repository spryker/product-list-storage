<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\ProductListProductConcreteStorage;

use Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer;

interface ProductListProductConcreteStorageReaderInterface
{
    public function findProductConcreteProductListStorage(int $idProduct): ?ProductConcreteProductListStorageTransfer;

    /**
     * @param array<int> $productConcreteIds
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer>
     */
    public function getProductConcreteProductListStorageTransfersByProductConcreteIds(array $productConcreteIds): array;
}
