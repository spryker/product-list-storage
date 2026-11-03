<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\Plugin\Catalog;

use Generated\Shared\Transfer\ProductConcreteCriteriaFilterTransfer;
use Spryker\Client\CatalogExtension\Dependency\Plugin\ProductConcreteStorageSearchResultFilterPluginInterface;
use Spryker\Client\Kernel\AbstractPlugin;

/**
 * @method \Spryker\Client\ProductListStorage\ProductListStorageFactory getFactory()
 */
class ProductListProductConcreteStorageSearchResultFilterPlugin extends AbstractPlugin implements ProductConcreteStorageSearchResultFilterPluginInterface
{
    /**
     * {@inheritDoc}
     * - Does nothing when `ProductConcreteCriteriaFilterTransfer.requestParams` has no `idProductList`.
     * - Reads the product list storage data of all given concrete products in one bulk storage call.
     * - Keeps only the concrete products that have the requested product list among their own whitelists or blacklists
     *   (`ProductConcreteProductListStorageTransfer.ownProductLists`).
     * - Drops concrete products that have no product list storage data, as they belong to no product list.
     * - Keeps concrete products whose product list storage data has no own product lists.
     * - Own product lists are published only when {@link \Spryker\Zed\ProductListStorage\ProductListStorageConfig::isProductConcreteOwnProductListsPublishingEnabled()} is enabled.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\ProductConcretePageSearchTransfer> $productConcretePageSearchTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductConcretePageSearchTransfer>
     */
    public function filter(
        array $productConcretePageSearchTransfers,
        ProductConcreteCriteriaFilterTransfer $productConcreteCriteriaFilterTransfer
    ): array {
        return $this->getFactory()
            ->createProductListProductConcreteSearchResultFilter()
            ->filterByProductList($productConcretePageSearchTransfers, $productConcreteCriteriaFilterTransfer);
    }
}
