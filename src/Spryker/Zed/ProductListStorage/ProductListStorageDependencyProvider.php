<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListStorage;

use Orm\Zed\Product\Persistence\SpyProductQuery;
use Orm\Zed\ProductCategory\Persistence\SpyProductCategoryQuery;
use Orm\Zed\ProductList\Persistence\SpyProductListProductConcreteQuery;
use Orm\Zed\ProductList\Persistence\SpyProductListQuery;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\ProductListStorage\Dependency\Facade\ProductListStorageToEventBehaviorFacadeBridge;
use Spryker\Zed\ProductListStorage\Dependency\Facade\ProductListStorageToProductListFacadeBridge;

/**
 * @method \Spryker\Zed\ProductListStorage\ProductListStorageConfig getConfig()
 */
class ProductListStorageDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const PROPEL_QUERY_PRODUCT = 'PROPEL_QUERY_PRODUCT';

    /**
     * @var string
     */
    public const PROPEL_PRODUCT_LIST_QUERY = 'PROPEL_PRODUCT_LIST_QUERY';

    /**
     * @var string
     */
    public const PROPEL_QUERY_PRODUCT_CATEGORY = 'PROPEL_QUERY_PRODUCT_CATEGORY';

    /**
     * @var string
     */
    public const FACADE_EVENT_BEHAVIOR = 'FACADE_EVENT_BEHAVIOR';

    /**
     * @var string
     */
    public const FACADE_PRODUCT_LIST = 'FACADE_PRODUCT_LIST';

    /**
     * @var string
     */
    public const PROPEL_QUERY_PRODUCT_LIST_PRODUCT_CONCRETE = 'PROPEL_QUERY_PRODUCT_LIST_PRODUCT_CONCRETE';

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addProductListFacade($container);

        return $container;
    }

    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);
        $container = $this->addEventBehaviorFacade($container);
        $container = $this->addProductListFacade($container);

        return $container;
    }

    public function providePersistenceLayerDependencies(Container $container): Container
    {
        $container = parent::providePersistenceLayerDependencies($container);
        $container = $this->addProductPropelQuery($container);
        $container = $this->addProductListPropelQuery($container);
        $container = $this->addProductCategoryPropelQuery($container);
        $container = $this->addProductListProductConcretePropelQuery($container);

        return $container;
    }

    protected function addProductListPropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_PRODUCT_LIST_QUERY, $container->factory(function () {
            return SpyProductListQuery::create();
        }));

        return $container;
    }

    protected function addEventBehaviorFacade(Container $container): Container
    {
        $container->set(static::FACADE_EVENT_BEHAVIOR, function (Container $container) {
            return new ProductListStorageToEventBehaviorFacadeBridge(
                $container->getLocator()->eventBehavior()->facade(),
            );
        });

        return $container;
    }

    protected function addProductListFacade(Container $container): Container
    {
        $container->set(static::FACADE_PRODUCT_LIST, function (Container $container) {
            return new ProductListStorageToProductListFacadeBridge($container->getLocator()->productList()->facade());
        });

        return $container;
    }

    protected function addProductPropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_PRODUCT, $container->factory(function () {
            return SpyProductQuery::create();
        }));

        return $container;
    }

    protected function addProductCategoryPropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_PRODUCT_CATEGORY, $container->factory(function () {
            return SpyProductCategoryQuery::create();
        }));

        return $container;
    }

    protected function addProductListProductConcretePropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_PRODUCT_LIST_PRODUCT_CONCRETE, $container->factory(function () {
            return SpyProductListProductConcreteQuery::create();
        }));

        return $container;
    }
}
