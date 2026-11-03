<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Client\ProductListStorage\Plugin\Catalog;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductConcreteCriteriaFilterTransfer;
use Generated\Shared\Transfer\ProductConcretePageSearchTransfer;
use Generated\Shared\Transfer\ProductListTransfer;
use Spryker\Client\ProductListStorage\Dependency\Client\ProductListStorageToStorageClientInterface;
use Spryker\Client\ProductListStorage\Plugin\Catalog\ProductListProductConcreteStorageSearchResultFilterPlugin;
use Spryker\Client\ProductListStorage\ProductListStorageDependencyProvider;
use SprykerTest\Client\ProductListStorage\ProductListStorageClientTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Client
 * @group ProductListStorage
 * @group Plugin
 * @group Catalog
 * @group ProductListProductConcreteStorageSearchResultFilterPluginTest
 * Add your own group annotations below this line
 */
class ProductListProductConcreteStorageSearchResultFilterPluginTest extends Unit
{
    protected const int ID_PRODUCT_LIST = 10;

    protected const int ID_PRODUCT_CONCRETE_IN_LIST = 1;

    protected const int ID_PRODUCT_CONCRETE_NOT_IN_LIST = 2;

    protected ProductListStorageClientTester $tester;

    public function testFilterKeepsOnlyConcreteProductsWithRequestedProductListInPublishedOwnProductLists(): void
    {
        // Arrange
        $this->setStorageData([
            $this->createStorageData(static::ID_PRODUCT_CONCRETE_IN_LIST, [static::ID_PRODUCT_LIST]),
            $this->createStorageData(static::ID_PRODUCT_CONCRETE_NOT_IN_LIST, []),
        ]);

        // Act
        $productConcretePageSearchTransfers = (new ProductListProductConcreteStorageSearchResultFilterPlugin())->filter(
            $this->createProductConcretePageSearchTransfers(),
            (new ProductConcreteCriteriaFilterTransfer())->setRequestParams([ProductListTransfer::ID_PRODUCT_LIST => (string)static::ID_PRODUCT_LIST]),
        );

        // Assert
        $this->assertCount(1, $productConcretePageSearchTransfers);
        $this->assertSame(static::ID_PRODUCT_CONCRETE_IN_LIST, $productConcretePageSearchTransfers[0]->getFkProduct());
    }

    public function testFilterReturnsConcreteProductsUntouchedWithoutRequestedProductList(): void
    {
        // Arrange
        $productConcretePageSearchTransfers = $this->createProductConcretePageSearchTransfers();

        // Act
        $filteredProductConcretePageSearchTransfers = (new ProductListProductConcreteStorageSearchResultFilterPlugin())->filter(
            $productConcretePageSearchTransfers,
            new ProductConcreteCriteriaFilterTransfer(),
        );

        // Assert
        $this->assertSame($productConcretePageSearchTransfers, $filteredProductConcretePageSearchTransfers);
    }

    /**
     * @param array<string> $storageData
     */
    protected function setStorageData(array $storageData): void
    {
        $storageClientMock = $this->getMockBuilder(ProductListStorageToStorageClientInterface::class)->getMock();
        $storageClientMock->method('getMulti')->willReturn($storageData);

        $this->tester->setDependency(ProductListStorageDependencyProvider::CLIENT_STORAGE, $storageClientMock);
    }

    /**
     * @param array<int> $ownWhitelistIds
     */
    protected function createStorageData(int $idProductConcrete, array $ownWhitelistIds): string
    {
        return (string)json_encode([
            'id_product_concrete' => $idProductConcrete,
            'id_blacklists' => [],
            'id_whitelists' => [static::ID_PRODUCT_LIST],
            'own_product_lists' => [
                'blacklist_ids' => [],
                'whitelist_ids' => $ownWhitelistIds,
            ],
        ]);
    }

    /**
     * @return array<\Generated\Shared\Transfer\ProductConcretePageSearchTransfer>
     */
    protected function createProductConcretePageSearchTransfers(): array
    {
        return [
            (new ProductConcretePageSearchTransfer())->setFkProduct(static::ID_PRODUCT_CONCRETE_IN_LIST),
            (new ProductConcretePageSearchTransfer())->setFkProduct(static::ID_PRODUCT_CONCRETE_NOT_IN_LIST),
        ];
    }
}
