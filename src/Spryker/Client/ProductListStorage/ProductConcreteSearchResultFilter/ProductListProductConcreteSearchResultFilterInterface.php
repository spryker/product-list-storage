<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\ProductConcreteSearchResultFilter;

use Generated\Shared\Transfer\ProductConcreteCriteriaFilterTransfer;

interface ProductListProductConcreteSearchResultFilterInterface
{
    /**
     * @param array<\Generated\Shared\Transfer\ProductConcretePageSearchTransfer> $productConcretePageSearchTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductConcretePageSearchTransfer>
     */
    public function filterByProductList(
        array $productConcretePageSearchTransfers,
        ProductConcreteCriteriaFilterTransfer $productConcreteCriteriaFilterTransfer
    ): array;
}
