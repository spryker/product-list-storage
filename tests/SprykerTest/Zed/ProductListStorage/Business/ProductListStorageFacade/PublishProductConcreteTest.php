<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductListStorage\Business\ProductListStorageFacade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\ProductListProductConcreteRelationTransfer;
use Generated\Shared\Transfer\ProductListTransfer;
use Orm\Zed\ProductList\Persistence\Map\SpyProductListTableMap;
use Orm\Zed\ProductListStorage\Persistence\SpyProductConcreteProductListStorageQuery;
use Spryker\Zed\ProductListStorage\Business\ProductListStorageBusinessFactory;
use Spryker\Zed\ProductListStorage\Business\ProductListStorageFacade;
use Spryker\Zed\ProductListStorage\Business\ProductListStorageFacadeInterface;
use Spryker\Zed\ProductListStorage\ProductListStorageConfig;
use SprykerTest\Zed\ProductListStorage\ProductListStorageBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductListStorage
 * @group Business
 * @group ProductListStorageFacade
 * @group PublishProductConcreteTest
 * Add your own group annotations below this line
 */
class PublishProductConcreteTest extends Unit
{
    protected ProductListStorageBusinessTester $tester;

    public function testPublishProductConcreteStoresOwnWhitelistsSeparatelyFromAbstractMergedWhitelists(): void
    {
        // Arrange
        [$productConcreteInListTransfer, $siblingProductConcreteTransfer] = $this->haveProductConcretesOfOneProductAbstract();
        $productListTransfer = $this->haveProductListWithProductConcretes(SpyProductListTableMap::COL_TYPE_WHITELIST, [$productConcreteInListTransfer]);

        // Act
        $this->getFacade(true)->publishProductConcrete([
            $productConcreteInListTransfer->getIdProductConcreteOrFail(),
            $siblingProductConcreteTransfer->getIdProductConcreteOrFail(),
        ]);

        // Assert
        $productConcreteInListStorageTransfer = $this->getProductConcreteProductListStorageTransfer($productConcreteInListTransfer);
        $siblingProductConcreteStorageTransfer = $this->getProductConcreteProductListStorageTransfer($siblingProductConcreteTransfer);

        $this->assertContains($productListTransfer->getIdProductList(), $siblingProductConcreteStorageTransfer->getIdWhitelists());
        $this->assertContains($productListTransfer->getIdProductList(), $productConcreteInListStorageTransfer->getOwnProductListsOrFail()->getWhitelistIds());
        $this->assertSame([], $siblingProductConcreteStorageTransfer->getOwnProductListsOrFail()->getWhitelistIds());
    }

    public function testPublishProductConcreteStoresOwnBlacklists(): void
    {
        // Arrange
        [$productConcreteInListTransfer] = $this->haveProductConcretesOfOneProductAbstract();
        $productListTransfer = $this->haveProductListWithProductConcretes(SpyProductListTableMap::COL_TYPE_BLACKLIST, [$productConcreteInListTransfer]);

        // Act
        $this->getFacade(true)->publishProductConcrete([$productConcreteInListTransfer->getIdProductConcreteOrFail()]);

        // Assert
        $productConcreteProductListStorageTransfer = $this->getProductConcreteProductListStorageTransfer($productConcreteInListTransfer);

        $this->assertContains($productListTransfer->getIdProductList(), $productConcreteProductListStorageTransfer->getOwnProductListsOrFail()->getBlacklistIds());
        $this->assertSame([], $productConcreteProductListStorageTransfer->getOwnProductListsOrFail()->getWhitelistIds());
    }

    public function testPublishProductConcreteDoesNotStoreOwnProductListsWhenPublishingDisabled(): void
    {
        // Arrange
        [$productConcreteInListTransfer] = $this->haveProductConcretesOfOneProductAbstract();
        $productListTransfer = $this->haveProductListWithProductConcretes(SpyProductListTableMap::COL_TYPE_WHITELIST, [$productConcreteInListTransfer]);

        // Act
        $this->getFacade(false)->publishProductConcrete([$productConcreteInListTransfer->getIdProductConcreteOrFail()]);

        // Assert
        $productConcreteProductListStorageTransfer = $this->getProductConcreteProductListStorageTransfer($productConcreteInListTransfer);

        $this->assertContains($productListTransfer->getIdProductList(), $productConcreteProductListStorageTransfer->getIdWhitelists());
        $this->assertNull($productConcreteProductListStorageTransfer->getOwnProductLists());
    }

    public function testPublishProductConcreteStoresCategoryProductListsAsOwnProductListsOfEveryConcreteProduct(): void
    {
        // Arrange
        [$productConcreteTransfer, $siblingProductConcreteTransfer] = $this->haveProductConcretesOfOneProductAbstract();
        $categoryTransfer = $this->tester->haveCategory();
        $this->tester->getLocator()->productCategory()->facade()->createProductCategoryMappings(
            $categoryTransfer->getIdCategoryOrFail(),
            [$productConcreteTransfer->getFkProductAbstractOrFail()],
        );
        $productListTransfer = $this->tester->haveProductList([ProductListTransfer::TYPE => SpyProductListTableMap::COL_TYPE_WHITELIST]);
        $this->tester->haveProductListCategory($productListTransfer, $categoryTransfer);

        // Act
        $this->getFacade(true)->publishProductConcrete([
            $productConcreteTransfer->getIdProductConcreteOrFail(),
            $siblingProductConcreteTransfer->getIdProductConcreteOrFail(),
        ]);

        // Assert
        $this->assertSame(
            [$productListTransfer->getIdProductList()],
            $this->getProductConcreteProductListStorageTransfer($productConcreteTransfer)->getOwnProductListsOrFail()->getWhitelistIds(),
        );
        $this->assertSame(
            [$productListTransfer->getIdProductList()],
            $this->getProductConcreteProductListStorageTransfer($siblingProductConcreteTransfer)->getOwnProductListsOrFail()->getWhitelistIds(),
        );
    }

    public function testPublishProductConcreteAddsOwnProductListsToExistingStorageDataWhenRepublishedAfterEnabling(): void
    {
        // Arrange
        [$productConcreteInListTransfer] = $this->haveProductConcretesOfOneProductAbstract();
        $productListTransfer = $this->haveProductListWithProductConcretes(SpyProductListTableMap::COL_TYPE_WHITELIST, [$productConcreteInListTransfer]);
        $this->getFacade(false)->publishProductConcrete([$productConcreteInListTransfer->getIdProductConcreteOrFail()]);

        // Act
        $this->getFacade(true)->publishProductConcrete([$productConcreteInListTransfer->getIdProductConcreteOrFail()]);

        // Assert
        $productConcreteProductListStorageCount = SpyProductConcreteProductListStorageQuery::create()
            ->filterByFkProduct($productConcreteInListTransfer->getIdProductConcreteOrFail())
            ->count();
        $productConcreteProductListStorageTransfer = $this->getProductConcreteProductListStorageTransfer($productConcreteInListTransfer);

        $this->assertSame(1, $productConcreteProductListStorageCount);
        $this->assertSame([$productListTransfer->getIdProductList()], $productConcreteProductListStorageTransfer->getOwnProductListsOrFail()->getWhitelistIds());
    }

    /**
     * @return array<\Generated\Shared\Transfer\ProductConcreteTransfer>
     */
    protected function haveProductConcretesOfOneProductAbstract(): array
    {
        $productConcreteTransfer = $this->tester->haveProduct();
        $siblingProductConcreteTransfer = $this->tester->haveProductConcrete([
            ProductConcreteTransfer::FK_PRODUCT_ABSTRACT => $productConcreteTransfer->getFkProductAbstractOrFail(),
            ProductConcreteTransfer::ABSTRACT_SKU => $productConcreteTransfer->getAbstractSkuOrFail(),
        ]);

        return [$productConcreteTransfer, $siblingProductConcreteTransfer];
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductConcreteTransfer> $productConcreteTransfers
     */
    protected function haveProductListWithProductConcretes(string $type, array $productConcreteTransfers): ProductListTransfer
    {
        $productListTransfer = $this->tester->haveProductList([ProductListTransfer::TYPE => $type]);
        $productListProductConcreteRelationTransfer = (new ProductListProductConcreteRelationTransfer())
            ->setIdProductList($productListTransfer->getIdProductList());

        foreach ($productConcreteTransfers as $productConcreteTransfer) {
            $productListProductConcreteRelationTransfer->addProductIds($productConcreteTransfer->getIdProductConcreteOrFail());
        }

        $productListTransfer->setProductListProductConcreteRelation($productListProductConcreteRelationTransfer);

        return $this->tester->getLocator()->productList()->facade()->saveProductList($productListTransfer);
    }

    protected function getProductConcreteProductListStorageTransfer(ProductConcreteTransfer $productConcreteTransfer): ProductConcreteProductListStorageTransfer
    {
        $productConcreteProductListStorageEntity = SpyProductConcreteProductListStorageQuery::create()
            ->filterByFkProduct($productConcreteTransfer->getIdProductConcreteOrFail())
            ->findOne();

        $this->assertNotNull($productConcreteProductListStorageEntity);

        return (new ProductConcreteProductListStorageTransfer())->fromArray($productConcreteProductListStorageEntity->getData(), true);
    }

    protected function getFacade(bool $isProductConcreteOwnProductListsPublishingEnabled): ProductListStorageFacadeInterface
    {
        $productListStorageConfigMock = $this->getMockBuilder(ProductListStorageConfig::class)
            ->onlyMethods(['isSendingToQueue', 'isProductConcreteOwnProductListsPublishingEnabled'])
            ->getMock();
        $productListStorageConfigMock->method('isSendingToQueue')->willReturn(false);
        $productListStorageConfigMock->method('isProductConcreteOwnProductListsPublishingEnabled')
            ->willReturn($isProductConcreteOwnProductListsPublishingEnabled);

        $productListStorageBusinessFactory = new ProductListStorageBusinessFactory();
        $productListStorageBusinessFactory->setConfig($productListStorageConfigMock);

        $productListStorageFacade = new ProductListStorageFacade();
        $productListStorageFacade->setFactory($productListStorageBusinessFactory);

        return $productListStorageFacade;
    }
}
