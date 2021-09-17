<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductListStorage\Communication\Plugin\Synchronization;

use Generated\Shared\Transfer\SynchronizationDataTransfer;
use Spryker\Shared\ProductListStorage\ProductListStorageConfig;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\SynchronizationExtension\Dependency\Plugin\SynchronizationDataRepositoryPluginInterface;

/**
 * @deprecated Use {@link \Spryker\Zed\ProductListStorage\Communication\Plugin\Synchronization\ProductConcreteProductListSynchronizationDataBulkPlugin} instead.
 *
 * @method \Spryker\Zed\ProductListStorage\ProductListStorageConfig getConfig()
 * @method \Spryker\Zed\ProductListStorage\Persistence\ProductListStorageRepositoryInterface getRepository()
 * @method \Spryker\Zed\ProductListStorage\Business\ProductListStorageFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductListStorage\Communication\ProductListStorageCommunicationFactory getFactory()
 */
class ProductConcreteProductListSynchronizationDataPlugin extends AbstractPlugin implements SynchronizationDataRepositoryPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getResourceName(): string
    {
        return ProductListStorageConfig::PRODUCT_LIST_CONCRETE_RESOURCE_NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return bool
     */
    public function hasStore(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return array
     */
    public function getParams(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getQueueName(): string
    {
        return ProductListStorageConfig::PRODUCT_LIST_CONCRETE_SYNC_STORAGE_QUEUE;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string|null
     */
    public function getSynchronizationQueuePoolName(): ?string
    {
        return $this->getConfig()->getProductConcreteProductListSynchronizationPoolName();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<int> $ids
     *
     * @return array<\Generated\Shared\Transfer\SynchronizationDataTransfer>
     */
    public function getData(array $ids = [])
    {
        $spyProductConcreteProductListStorageEntities = $this->findSpyProductConcreteProductListStorageEntities($ids);

        $synchronizationDataTransfers = [];
        foreach ($spyProductConcreteProductListStorageEntities as $spyProductConcreteProductListStorageEntity) {
            $synchronizationDataTransfer = new SynchronizationDataTransfer();
            /** @var string $data */
            $data = $spyProductConcreteProductListStorageEntity->getData();
            $synchronizationDataTransfer->setData($data);
            $synchronizationDataTransfer->setKey($spyProductConcreteProductListStorageEntity->getKey());
            $synchronizationDataTransfers[] = $synchronizationDataTransfer;
        }

        return $synchronizationDataTransfers;
    }

    /**
     * @param array $ids
     *
     * @return array<\Orm\Zed\ProductListStorage\Persistence\SpyProductConcreteProductListStorage>
     */
    protected function findSpyProductConcreteProductListStorageEntities(array $ids = []): array
    {
        if ($ids === []) {
            return $this->getRepository()->findAllProductConcreteProductListStorageEntities();
        }

        return $this->getRepository()->findProductConcreteProductListStorageEntities($ids);
    }
}
