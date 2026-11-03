<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListStorage;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class ProductListStorageConfig extends AbstractBundleConfig
{
    /**
     * @var int
     */
    protected const PRODUCT_LIST_PRODUCT_ABSTRACT_PUBLISH_CHUNK_SIZE = 500;

    /**
     * @var int
     */
    protected const PRODUCT_LIST_PRODUCT_CONCRETE_PUBLISH_CHUNK_SIZE = 500;

    /**
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\SynchronizationBehavior\SynchronizationBehaviorConfig::isSynchronizationEnabled()} instead.
     *
     * @return bool
     */
    public function isSendingToQueue(): bool
    {
        return true;
    }

    /**
     * @api
     *
     * @return string|null
     */
    public function getProductAbstractProductListSynchronizationPoolName(): ?string
    {
        return null;
    }

    /**
     * @api
     *
     * @return string|null
     */
    public function getProductConcreteProductListSynchronizationPoolName(): ?string
    {
        return null;
    }

    /**
     * @api
     *
     * @return string|null
     */
    public function getProductAbstractProductListEventQueueName(): ?string
    {
        return null;
    }

    /**
     * @api
     *
     * @return string|null
     */
    public function getProductConcreteProductListEventQueueName(): ?string
    {
        return null;
    }

    /**
     * @api
     *
     * @return int
     */
    public function getProductListProductAbstractPublishChunkSize(): int
    {
        return static::PRODUCT_LIST_PRODUCT_ABSTRACT_PUBLISH_CHUNK_SIZE;
    }

    /**
     * @api
     *
     * @return int
     */
    public function getProductListProductConcretePublishChunkSize(): int
    {
        return static::PRODUCT_LIST_PRODUCT_CONCRETE_PUBLISH_CHUNK_SIZE;
    }

    /**
     * @api
     *
     * @return string|null
     */
    public function getProductListEventQueueName(): ?string
    {
        return null;
    }

    /**
     * Specification:
     * - Defines if the concrete product product list storage data is extended with `ownProductLists`.
     * - `ownProductLists` contains the product lists assigned to the concrete product itself and to the categories of its abstract product,
     *   without the lists of its sibling concrete products that `idWhitelists`/`idBlacklists` also contain.
     * - Required by {@link \Spryker\Client\ProductListStorage\Plugin\Catalog\ProductListProductConcreteStorageSearchResultFilterPlugin}:
     *   while `ownProductLists` is not published, the plugin keeps all concrete products that have product list storage data.
     * - After enabling, re-publish the concrete product product lists, e.g. `vendor/bin/console publish:trigger-events -r product_list`.
     *
     * @api
     */
    public function isProductConcreteOwnProductListsPublishingEnabled(): bool
    {
        return false;
    }
}
