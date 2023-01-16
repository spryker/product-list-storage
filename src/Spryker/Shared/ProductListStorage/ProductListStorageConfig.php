<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\ProductListStorage;

use Spryker\Shared\Kernel\AbstractBundleConfig;

class ProductListStorageConfig extends AbstractBundleConfig
{
    /**
     * Specification:
     * - Queue name as used for processing price messages
     *
     * @api
     *
     * @var string
     */
    public const PRODUCT_LIST_ABSTRACT_SYNC_STORAGE_QUEUE = 'sync.storage.product';

    /**
     * Specification:
     * - Queue name as used for processing price messages
     *
     * @api
     *
     * @var string
     */
    public const PRODUCT_LIST_CONCRETE_SYNC_STORAGE_QUEUE = 'sync.storage.product';

    /**
     * Specification:
     * - Key generation resource name of product abstract lists.
     *
     * @api
     *
     * @var string
     */
    public const PRODUCT_LIST_ABSTRACT_RESOURCE_NAME = 'product_abstract_product_lists';

    /**
     * Specification:
     * - Key generation resource name of product concrete lists.
     *
     * @api
     *
     * @var string
     */
    public const PRODUCT_LIST_CONCRETE_RESOURCE_NAME = 'product_concrete_product_lists';

    /**
     * Specification:
     *  - Product list resource name, used for key generation.
     *
     * @api
     *
     * @var string
     */
    public const PRODUCT_LIST_RESOURCE_NAME = 'product_list';

    /**
     * @uses \Spryker\Shared\Product\ProductConfig::VARIANT_LEAF_NODE_ID
     *
     * @var string
     */
    public const VARIANT_LEAF_NODE_ID = 'id_product_concrete';

    /**
     * @uses \Spryker\Shared\Product\ProductConfig::ATTRIBUTE_MAP_PATH_DELIMITER
     *
     * @phpstan-var non-empty-string
     *
     * @var string
     */
    public const ATTRIBUTE_MAP_PATH_DELIMITER = ':';

    /**
     * Specification:
     * - This event is used for product list publishing.
     *
     * @api
     *
     * @var string
     */
    public const PRODUCT_LIST_PUBLISH = 'ProductList.spy_product_list.publish';
}
