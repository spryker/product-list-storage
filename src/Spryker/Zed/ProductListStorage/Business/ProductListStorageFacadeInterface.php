<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListStorage\Business;

interface ProductListStorageFacadeInterface
{
    /**
     * Specification:
     * - Publishes abstract product list changes to storage.
     *
     * @api
     *
     * @param array<int> $productAbstractIds
     *
     * @return void
     */
    public function publishProductAbstract(array $productAbstractIds): void;

    /**
     * Specification:
     * - Publishes concrete product list changes to storage.
     *
     * @api
     *
     * @param array<int> $productConcreteIds
     *
     * @return void
     */
    public function publishProductConcrete(array $productConcreteIds): void;

    /**
     * Specification:
     *  - Retrieve list of abstract product ids by concrete product ids.
     *
     * @api
     *
     * @param array<int> $productConcreteIds
     *
     * @return array<int>
     */
    public function findProductAbstractIdsByProductConcreteIds(array $productConcreteIds): array;

    /**
     * Specification:
     *  - Retrieve list of abstract product ids by category ids.
     *
     * @api
     *
     * @param array<int> $categoryIds
     *
     * @return array<int>
     */
    public function getProductAbstractIdsByCategoryIds(array $categoryIds): array;

    /**
     * Specification:
     *  - Retrieve list of concrete product ids by abstract product ids.
     *
     * @api
     *
     * @param array<int> $productAbstractIds
     *
     * @return array<int>
     */
    public function findProductConcreteIdsByProductAbstractIds(array $productAbstractIds): array;

    /**
     * Specification:
     * - Publishes product list changes to abstract and concrete storage.
     *
     * @api
     *
     * @param array<int> $productListIds
     *
     * @return void
     */
    public function publishProductList(array $productListIds): void;
}
