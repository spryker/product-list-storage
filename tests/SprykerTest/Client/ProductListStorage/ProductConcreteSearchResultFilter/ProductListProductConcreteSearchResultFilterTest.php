<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Client\ProductListStorage\ProductConcreteSearchResultFilter;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductConcreteCriteriaFilterTransfer;
use Generated\Shared\Transfer\ProductConcreteOwnProductListsStorageTransfer;
use Generated\Shared\Transfer\ProductConcretePageSearchTransfer;
use Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer;
use Generated\Shared\Transfer\ProductListTransfer;
use Spryker\Client\ProductListStorage\ProductConcreteSearchResultFilter\ProductListProductConcreteSearchResultFilter;
use Spryker\Client\ProductListStorage\ProductListProductConcreteStorage\ProductListProductConcreteStorageReaderInterface;
use SprykerTest\Client\ProductListStorage\ProductListStorageClientTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Client
 * @group ProductListStorage
 * @group ProductConcreteSearchResultFilter
 * @group ProductListProductConcreteSearchResultFilterTest
 * Add your own group annotations below this line
 */
class ProductListProductConcreteSearchResultFilterTest extends Unit
{
    protected const int ID_PRODUCT_LIST = 10;

    protected const int ID_OTHER_PRODUCT_LIST = 20;

    protected const int ID_PRODUCT_CONCRETE_IN_LIST = 1;

    protected const int ID_PRODUCT_CONCRETE_NOT_IN_LIST = 2;

    protected const int ID_PRODUCT_CONCRETE_WITHOUT_STORAGE_DATA = 3;

    protected const int ID_PRODUCT_CONCRETE_WITHOUT_OWN_PRODUCT_LISTS = 4;

    protected ProductListStorageClientTester $tester;

    public function testFilterByProductListKeepsOnlyConcreteProductsWithRequestedProductListInOwnWhitelists(): void
    {
        // Arrange
        $productListProductConcreteStorageReaderMock = $this->createProductListProductConcreteStorageReaderMock([
            $this->createProductConcreteProductListStorageTransfer(static::ID_PRODUCT_CONCRETE_IN_LIST, [static::ID_PRODUCT_LIST], []),
            $this->createProductConcreteProductListStorageTransfer(static::ID_PRODUCT_CONCRETE_NOT_IN_LIST, [static::ID_OTHER_PRODUCT_LIST], []),
        ]);
        $productConcretePageSearchTransfers = [
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_IN_LIST),
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_NOT_IN_LIST),
        ];

        // Act
        $filteredProductConcretePageSearchTransfers = (new ProductListProductConcreteSearchResultFilter($productListProductConcreteStorageReaderMock))
            ->filterByProductList($productConcretePageSearchTransfers, $this->createProductConcreteCriteriaFilterTransfer(static::ID_PRODUCT_LIST));

        // Assert
        $this->assertCount(1, $filteredProductConcretePageSearchTransfers);
        $this->assertSame(static::ID_PRODUCT_CONCRETE_IN_LIST, $filteredProductConcretePageSearchTransfers[0]->getFkProduct());
    }

    public function testFilterByProductListKeepsConcreteProductsWithRequestedProductListInOwnBlacklists(): void
    {
        // Arrange
        $productListProductConcreteStorageReaderMock = $this->createProductListProductConcreteStorageReaderMock([
            $this->createProductConcreteProductListStorageTransfer(static::ID_PRODUCT_CONCRETE_IN_LIST, [], [static::ID_PRODUCT_LIST]),
            $this->createProductConcreteProductListStorageTransfer(static::ID_PRODUCT_CONCRETE_NOT_IN_LIST, [], [static::ID_OTHER_PRODUCT_LIST]),
        ]);
        $productConcretePageSearchTransfers = [
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_IN_LIST),
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_NOT_IN_LIST),
        ];

        // Act
        $filteredProductConcretePageSearchTransfers = (new ProductListProductConcreteSearchResultFilter($productListProductConcreteStorageReaderMock))
            ->filterByProductList($productConcretePageSearchTransfers, $this->createProductConcreteCriteriaFilterTransfer(static::ID_PRODUCT_LIST));

        // Assert
        $this->assertCount(1, $filteredProductConcretePageSearchTransfers);
        $this->assertSame(static::ID_PRODUCT_CONCRETE_IN_LIST, $filteredProductConcretePageSearchTransfers[0]->getFkProduct());
    }

    public function testFilterByProductListDropsConcreteProductsWithoutProductListStorageData(): void
    {
        // Arrange
        $productListProductConcreteStorageReaderMock = $this->createProductListProductConcreteStorageReaderMock([
            $this->createProductConcreteProductListStorageTransfer(static::ID_PRODUCT_CONCRETE_IN_LIST, [static::ID_PRODUCT_LIST], []),
        ]);
        $productConcretePageSearchTransfers = [
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_IN_LIST),
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_WITHOUT_STORAGE_DATA),
        ];

        // Act
        $filteredProductConcretePageSearchTransfers = (new ProductListProductConcreteSearchResultFilter($productListProductConcreteStorageReaderMock))
            ->filterByProductList($productConcretePageSearchTransfers, $this->createProductConcreteCriteriaFilterTransfer(static::ID_PRODUCT_LIST));

        // Assert
        $this->assertCount(1, $filteredProductConcretePageSearchTransfers);
        $this->assertSame(static::ID_PRODUCT_CONCRETE_IN_LIST, $filteredProductConcretePageSearchTransfers[0]->getFkProduct());
    }

    public function testFilterByProductListKeepsConcreteProductsWithoutOwnProductListsInStorage(): void
    {
        // Arrange
        $productListProductConcreteStorageReaderMock = $this->createProductListProductConcreteStorageReaderMock([
            $this->createProductConcreteProductListStorageTransfer(static::ID_PRODUCT_CONCRETE_IN_LIST, [static::ID_PRODUCT_LIST], []),
            (new ProductConcreteProductListStorageTransfer())
                ->setIdProductConcrete(static::ID_PRODUCT_CONCRETE_WITHOUT_OWN_PRODUCT_LISTS)
                ->setIdWhitelists([static::ID_PRODUCT_LIST]),
        ]);
        $productConcretePageSearchTransfers = [
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_IN_LIST),
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_WITHOUT_OWN_PRODUCT_LISTS),
        ];

        // Act
        $filteredProductConcretePageSearchTransfers = (new ProductListProductConcreteSearchResultFilter($productListProductConcreteStorageReaderMock))
            ->filterByProductList($productConcretePageSearchTransfers, $this->createProductConcreteCriteriaFilterTransfer(static::ID_PRODUCT_LIST));

        // Assert
        $this->assertSame($productConcretePageSearchTransfers, $filteredProductConcretePageSearchTransfers);
    }

    public function testFilterByProductListReturnsResultUntouchedWhenNoProductListIdRequested(): void
    {
        // Arrange
        $productListProductConcreteStorageReaderMock = $this->getMockBuilder(ProductListProductConcreteStorageReaderInterface::class)->getMock();
        $productListProductConcreteStorageReaderMock->expects($this->never())->method('getProductConcreteProductListStorageTransfersByProductConcreteIds');
        $productConcretePageSearchTransfers = [
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_IN_LIST),
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_NOT_IN_LIST),
        ];

        // Act
        $filteredProductConcretePageSearchTransfers = (new ProductListProductConcreteSearchResultFilter($productListProductConcreteStorageReaderMock))
            ->filterByProductList($productConcretePageSearchTransfers, new ProductConcreteCriteriaFilterTransfer());

        // Assert
        $this->assertSame($productConcretePageSearchTransfers, $filteredProductConcretePageSearchTransfers);
    }

    public function testFilterByProductListReadsProductListStorageDataInOneBulkCall(): void
    {
        // Arrange
        $productListProductConcreteStorageReaderMock = $this->getMockBuilder(ProductListProductConcreteStorageReaderInterface::class)->getMock();
        $productListProductConcreteStorageReaderMock->expects($this->once())
            ->method('getProductConcreteProductListStorageTransfersByProductConcreteIds')
            ->with([static::ID_PRODUCT_CONCRETE_IN_LIST, static::ID_PRODUCT_CONCRETE_NOT_IN_LIST])
            ->willReturn([
                $this->createProductConcreteProductListStorageTransfer(static::ID_PRODUCT_CONCRETE_IN_LIST, [static::ID_PRODUCT_LIST], []),
                $this->createProductConcreteProductListStorageTransfer(static::ID_PRODUCT_CONCRETE_NOT_IN_LIST, [], []),
            ]);
        $productListProductConcreteStorageReaderMock->expects($this->never())->method('findProductConcreteProductListStorage');
        $productConcretePageSearchTransfers = [
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_IN_LIST),
            $this->createProductConcretePageSearchTransfer(static::ID_PRODUCT_CONCRETE_NOT_IN_LIST),
        ];

        // Act
        $filteredProductConcretePageSearchTransfers = (new ProductListProductConcreteSearchResultFilter($productListProductConcreteStorageReaderMock))
            ->filterByProductList($productConcretePageSearchTransfers, $this->createProductConcreteCriteriaFilterTransfer(static::ID_PRODUCT_LIST));

        // Assert
        $this->assertCount(1, $filteredProductConcretePageSearchTransfers);
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductConcreteProductListStorageTransfer> $productConcreteProductListStorageTransfers
     */
    protected function createProductListProductConcreteStorageReaderMock(
        array $productConcreteProductListStorageTransfers
    ): ProductListProductConcreteStorageReaderInterface {
        $productListProductConcreteStorageReaderMock = $this->getMockBuilder(ProductListProductConcreteStorageReaderInterface::class)->getMock();
        $productListProductConcreteStorageReaderMock
            ->method('getProductConcreteProductListStorageTransfersByProductConcreteIds')
            ->willReturn($productConcreteProductListStorageTransfers);

        return $productListProductConcreteStorageReaderMock;
    }

    /**
     * @param array<int> $ownWhitelistIds
     * @param array<int> $ownBlacklistIds
     */
    protected function createProductConcreteProductListStorageTransfer(
        int $idProductConcrete,
        array $ownWhitelistIds,
        array $ownBlacklistIds
    ): ProductConcreteProductListStorageTransfer {
        return (new ProductConcreteProductListStorageTransfer())
            ->setIdProductConcrete($idProductConcrete)
            ->setIdWhitelists([static::ID_PRODUCT_LIST])
            ->setIdBlacklists([static::ID_PRODUCT_LIST])
            ->setOwnProductLists(
                (new ProductConcreteOwnProductListsStorageTransfer())
                    ->setWhitelistIds($ownWhitelistIds)
                    ->setBlacklistIds($ownBlacklistIds),
            );
    }

    protected function createProductConcretePageSearchTransfer(int $idProductConcrete): ProductConcretePageSearchTransfer
    {
        return (new ProductConcretePageSearchTransfer())->setFkProduct($idProductConcrete);
    }

    protected function createProductConcreteCriteriaFilterTransfer(int $idProductList): ProductConcreteCriteriaFilterTransfer
    {
        return (new ProductConcreteCriteriaFilterTransfer())
            ->setRequestParams([ProductListTransfer::ID_PRODUCT_LIST => $idProductList]);
    }
}
