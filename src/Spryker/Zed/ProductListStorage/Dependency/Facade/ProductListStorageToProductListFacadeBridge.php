<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListStorage\Dependency\Facade;

use Generated\Shared\Transfer\ProductListCollectionTransfer;
use Generated\Shared\Transfer\ProductListCriteriaTransfer;

class ProductListStorageToProductListFacadeBridge implements ProductListStorageToProductListFacadeInterface
{
    /**
     * @var \Spryker\Zed\ProductList\Business\ProductListFacadeInterface
     */
    protected $productListFacade;

    /**
     * @param \Spryker\Zed\ProductList\Business\ProductListFacadeInterface $productListFacade
     */
    public function __construct($productListFacade)
    {
        $this->productListFacade = $productListFacade;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array<int>
     */
    public function getProductBlacklistIdsByIdProductAbstract(int $idProductAbstract): array
    {
        return $this->productListFacade->getProductBlacklistIdsByIdProductAbstract($idProductAbstract);
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array<int>
     */
    public function getProductWhitelistIdsByIdProductAbstract(int $idProductAbstract): array
    {
        return $this->productListFacade->getProductWhitelistIdsByIdProductAbstract($idProductAbstract);
    }

    /**
     * @param int $idProductAbstract
     *
     * @return array<int>
     */
    public function getCategoryWhitelistIdsByIdProductAbstract(int $idProductAbstract): array
    {
        return $this->productListFacade->getCategoryWhitelistIdsByIdProductAbstract($idProductAbstract);
    }

    /**
     * @param int $idProduct
     *
     * @return array<int>
     */
    public function getProductBlacklistIdsByIdProduct(int $idProduct): array
    {
        return $this->productListFacade->getProductBlacklistIdsByIdProduct($idProduct);
    }

    /**
     * @param int $idProduct
     *
     * @return array<int>
     */
    public function getProductWhitelistIdsByIdProduct(int $idProduct): array
    {
        return $this->productListFacade->getProductWhitelistIdsByIdProduct($idProduct);
    }

    /**
     * @param array<int> $productListIds
     *
     * @return array<int>
     */
    public function getProductAbstractIdsByProductListIds(array $productListIds): array
    {
        return $this->productListFacade->getProductAbstractIdsByProductListIds($productListIds);
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return array
     */
    public function getProductAbstractListIdsByProductAbstractIds(array $productAbstractIds): array
    {
        return $this->productListFacade->getProductAbstractListIdsByProductAbstractIds($productAbstractIds);
    }

    /**
     * @param array<int> $productConcreteIds
     *
     * @return array
     */
    public function getProductListsIdsByProductIds(array $productConcreteIds): array
    {
        return $this->productListFacade->getProductListsIdsByProductIds($productConcreteIds);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductListCriteriaTransfer $productListCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductListCollectionTransfer
     */
    public function getProductListCollection(ProductListCriteriaTransfer $productListCriteriaTransfer): ProductListCollectionTransfer
    {
        return $this->productListFacade->getProductListCollection($productListCriteriaTransfer);
    }
}
