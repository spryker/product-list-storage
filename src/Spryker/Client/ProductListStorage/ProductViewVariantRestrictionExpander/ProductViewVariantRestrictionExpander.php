<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductListStorage\ProductViewVariantRestrictionExpander;

use Generated\Shared\Transfer\AttributeMapStorageTransfer;
use Generated\Shared\Transfer\ProductViewTransfer;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Spryker\Client\ProductListStorage\ProductConcreteRestriction\ProductConcreteRestrictionReaderInterface;

class ProductViewVariantRestrictionExpander implements ProductViewVariantRestrictionExpanderInterface
{
    protected const PATTERN_ATTRIBUTE_KEY_VALUE = '%s:%s';
    protected const ID_PRODUCT_CONCRETE = 'id_product_concrete';

    /**
     * @var \Spryker\Client\ProductListStorage\ProductConcreteRestriction\ProductConcreteRestrictionReaderInterface
     */
    protected $productConcreteRestrictionReader;

    /**
     * @param \Spryker\Client\ProductListStorage\ProductConcreteRestriction\ProductConcreteRestrictionReaderInterface $productConcreteRestrictionReader
     */
    public function __construct(
        ProductConcreteRestrictionReaderInterface $productConcreteRestrictionReader
    ) {
        $this->productConcreteRestrictionReader = $productConcreteRestrictionReader;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductViewTransfer $productViewTransfer
     *
     * @return \Generated\Shared\Transfer\ProductViewTransfer
     */
    public function expandProductVariantData(ProductViewTransfer $productViewTransfer): ProductViewTransfer
    {
        $attributeMapStorageTransfer = $productViewTransfer->getAttributeMap();
        if (!$attributeMapStorageTransfer) {
            return $productViewTransfer;
        }

        $this->filterRestrictedConcreteProducts($productViewTransfer->getAttributeMap());

        $availableAttributes = $this->getAvailableAttributes(
            $productViewTransfer->getAvailableAttributes(),
            $productViewTransfer->getAttributeMap()->getAttributeVariants(),
            $productViewTransfer->getSelectedAttributes()
        );
        $productViewTransfer->setAvailableAttributes($availableAttributes);

        return $productViewTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AttributeMapStorageTransfer $attributeMapStorageTransfer
     *
     * @return void
     */
    protected function filterRestrictedConcreteProducts(AttributeMapStorageTransfer $attributeMapStorageTransfer): void
    {
        $nonRestrictedProductConcreteIds = $this->getNonRestrictedProductConcreteIds($attributeMapStorageTransfer->getProductConcreteIds());

        $nonRestrictedAttributeVariants = [];
        foreach ($nonRestrictedProductConcreteIds as $nonRestrictedProductConcreteId) {
            $nonRestrictedAttributeVariants = array_merge_recursive($nonRestrictedAttributeVariants, $this->getNonRestrictedAttributeVariants(
                $nonRestrictedProductConcreteId,
                $attributeMapStorageTransfer->getAttributeVariants()
            ));
        }

        $nonRestrictedSuperAttributes = $this->getAvailableAttributes(
            $attributeMapStorageTransfer->getSuperAttributes(),
            $nonRestrictedAttributeVariants
        );

        $attributeMapStorageTransfer->setProductConcreteIds($nonRestrictedProductConcreteIds);
        $attributeMapStorageTransfer->setAttributeVariants($nonRestrictedAttributeVariants);
        $attributeMapStorageTransfer->setSuperAttributes($nonRestrictedSuperAttributes);
    }

    /**
     * @param array $productConcreteIds
     *
     * @return array
     */
    protected function getNonRestrictedProductConcreteIds(array $productConcreteIds): array
    {
        return array_filter($productConcreteIds, function ($productConcreteId) {
            return !$this->productConcreteRestrictionReader->isProductConcreteRestricted($productConcreteId);
        });
    }

    /**
     * @param int $nonRestrictedProductConcreteId
     * @param array $attributeVariants
     *
     * @return array
     */
    protected function getNonRestrictedAttributeVariants(int $nonRestrictedProductConcreteId, array $attributeVariants): array
    {
        $nonRestrictedAttributeVariants = [];
        $iterator = $this->createRecursiveIterator($attributeVariants);

        foreach ($iterator as $attributeVariantKey => $attributeVariantValue) {
            if (is_array($attributeVariantValue) && $this->isNonRestrictedAttributeVariant($attributeVariantValue, $nonRestrictedProductConcreteId)) {
                $variantPath = $this->buildVariantPath($iterator, $attributeVariantKey, $attributeVariantValue);
                $nonRestrictedAttributeVariants = array_merge_recursive($nonRestrictedAttributeVariants, $variantPath);
            }
        }

        return $nonRestrictedAttributeVariants;
    }

    /**
     * @param array $attributes
     * @param array $nonRestrictedAttributeVariants
     * @param array $selectedAttributes
     *
     * @return array
     */
    protected function getAvailableAttributes(array $attributes, array $nonRestrictedAttributeVariants, array $selectedAttributes = []): array
    {
        $availableAttributes = $this->getAvailableAttributesPerSelectedOptions($nonRestrictedAttributeVariants, $selectedAttributes);

        foreach ($attributes as $attributeKey => $attributeValues) {
            $availableValues = $this->getAvailableAttributeValues($attributeKey, $attributeValues, $nonRestrictedAttributeVariants);

            if (isset($availableAttributes[$attributeKey])) {
                $availableAttributes[$attributeKey] = array_intersect($availableAttributes[$attributeKey], $availableValues);
                continue;
            }

            $availableAttributes[$attributeKey] = $availableValues;
        }

        return $availableAttributes;
    }

    /**
     * @param array $nonRestrictedAttributeVariants
     * @param array $selectedAttributes
     *
     * @return array
     */
    protected function getAvailableAttributesPerSelectedOptions(array $nonRestrictedAttributeVariants, array $selectedAttributes = []): array
    {
        $availableAttributes = $availableAttributesPerSelectedOptions = [];

        foreach ($selectedAttributes as $key => $selectedAttribute) {
            $selectedAttributeKey = $this->getAttributeKeyValue($key, $selectedAttributes[$key]);

            if (isset($nonRestrictedAttributeVariants[$selectedAttributeKey])) {
                $availableAttributeKeys = $nonRestrictedAttributeVariants[$selectedAttributeKey];
                $availableAttributes = array_merge($availableAttributes, array_keys($availableAttributeKeys));
            }
        }

        $availableAttributes = array_unique($availableAttributes);

        foreach ($availableAttributes as $availableAttribute) {
            [$availableAttributeKey, $availableAttributeValue] = explode(':', $availableAttribute);
            $availableAttributesPerSelectedOptions[$availableAttributeKey][] = $availableAttributeValue;
        }

        return $availableAttributesPerSelectedOptions;
    }

    /**
     * @param string $attributeKey
     * @param array $attributeValues
     * @param array $nonRestrictedAttributeVariants
     *
     * @return array
     */
    protected function getAvailableAttributeValues(
        string $attributeKey,
        array $attributeValues,
        array $nonRestrictedAttributeVariants
    ): array {
        $availableAttributeValues = [];

        foreach ($attributeValues as $attributeValue) {
            $attributeKeyValue = $this->getAttributeKeyValue($attributeKey, $attributeValue);

            if ($this->isAttributeKeyValueAvailable($attributeKeyValue, $nonRestrictedAttributeVariants)) {
                $availableAttributeValues[] = $attributeValue;
            }
        }

        return $availableAttributeValues;
    }

    /**
     * @param string $attributeKeyValue
     * @param array $nonRestrictedAttributeVariants
     *
     * @return bool
     */
    protected function isAttributeKeyValueAvailable(string $attributeKeyValue, array $nonRestrictedAttributeVariants): bool
    {
        return array_key_exists($attributeKeyValue, $nonRestrictedAttributeVariants);
    }

    /**
     * @param \RecursiveIteratorIterator $iterator
     * @param string $attributeVariantKey
     * @param array $attributeVariantValue
     *
     * @return array
     */
    protected function buildVariantPath(
        RecursiveIteratorIterator $iterator,
        string $attributeVariantKey,
        array $attributeVariantValue
    ): array {
        $variantPath[$attributeVariantKey] = $attributeVariantValue;
        for ($i = $iterator->getDepth() - 1; $i >= 0; $i--) {
            $variantPath = [
                $iterator->getSubIterator($i)->key() => $variantPath,
            ];
        }

        return $variantPath;
    }

    /**
     * @param array $attributeVariantValue
     * @param int $nonRestrictedProductId
     *
     * @return bool
     */
    protected function isNonRestrictedAttributeVariant(array $attributeVariantValue, int $nonRestrictedProductId): bool
    {
        return array_key_exists(static::ID_PRODUCT_CONCRETE, $attributeVariantValue)
            && $attributeVariantValue[static::ID_PRODUCT_CONCRETE] === $nonRestrictedProductId;
    }

    /**
     * @param array $attributeVariants
     *
     * @return \RecursiveIteratorIterator
     */
    protected function createRecursiveIterator(array $attributeVariants): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(
            new RecursiveArrayIterator($attributeVariants),
            RecursiveIteratorIterator::SELF_FIRST
        );
    }

    /**
     * @param string $attributeKey
     * @param string $attributeValue
     *
     * @return string
     */
    protected function getAttributeKeyValue(string $attributeKey, string $attributeValue): string
    {
        return sprintf(
            static::PATTERN_ATTRIBUTE_KEY_VALUE,
            $attributeKey,
            $attributeValue
        );
    }
}
