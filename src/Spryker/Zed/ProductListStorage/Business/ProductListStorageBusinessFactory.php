<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListStorage\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\ProductListStorage\Business\ProductAbstract\ProductAbstractReader;
use Spryker\Zed\ProductListStorage\Business\ProductAbstract\ProductAbstractReaderInterface;
use Spryker\Zed\ProductListStorage\Business\ProductConcrete\ProductConcreteReader;
use Spryker\Zed\ProductListStorage\Business\ProductConcrete\ProductConcreteReaderInterface;
use Spryker\Zed\ProductListStorage\Business\ProductListProductAbstractStorage\ProductListProductAbstractStorageWriter;
use Spryker\Zed\ProductListStorage\Business\ProductListProductAbstractStorage\ProductListProductAbstractStorageWriterInterface;
use Spryker\Zed\ProductListStorage\Business\ProductListProductConcreteStorage\ProductListProductConcreteStorageWriter;
use Spryker\Zed\ProductListStorage\Business\ProductListProductConcreteStorage\ProductListProductConcreteStorageWriterInterface;
use Spryker\Zed\ProductListStorage\Business\ProductListStorage\ProductListStorageWriter;
use Spryker\Zed\ProductListStorage\Business\ProductListStorage\ProductListStorageWriterInterface;
use Spryker\Zed\ProductListStorage\Dependency\Facade\ProductListStorageToProductListFacadeInterface;
use Spryker\Zed\ProductListStorage\ProductListStorageDependencyProvider;

/**
 * @method \Spryker\Zed\ProductListStorage\Persistence\ProductListStorageRepositoryInterface getRepository()
 * @method \Spryker\Zed\ProductListStorage\ProductListStorageConfig getConfig()
 */
class ProductListStorageBusinessFactory extends AbstractBusinessFactory
{
    public function createProductListProductAbstractStorageWriter(): ProductListProductAbstractStorageWriterInterface
    {
        return new ProductListProductAbstractStorageWriter(
            $this->getProductListFacade(),
            $this->getRepository(),
            $this->getConfig(),
        );
    }

    public function createProductListProductConcreteStorageWriter(): ProductListProductConcreteStorageWriterInterface
    {
        return new ProductListProductConcreteStorageWriter(
            $this->getProductListFacade(),
            $this->getRepository(),
            $this->getConfig(),
        );
    }

    public function createProductAbstractReader(): ProductAbstractReaderInterface
    {
        return new ProductAbstractReader(
            $this->getRepository(),
        );
    }

    public function createProductConcreteReader(): ProductConcreteReaderInterface
    {
        return new ProductConcreteReader(
            $this->getRepository(),
        );
    }

    public function createProductListStorageWriter(): ProductListStorageWriterInterface
    {
        return new ProductListStorageWriter(
            $this->createProductListProductAbstractStorageWriter(),
            $this->createProductListProductConcreteStorageWriter(),
            $this->getProductListFacade(),
            $this->getRepository(),
        );
    }

    protected function getProductListFacade(): ProductListStorageToProductListFacadeInterface
    {
        return $this->getProvidedDependency(ProductListStorageDependencyProvider::FACADE_PRODUCT_LIST);
    }
}
