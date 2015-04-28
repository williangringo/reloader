<?php

require __DIR__ . '/../../../app/Mage.php';
require __DIR__ . '/../vendor/autoload.php';

function _save_image($img)
{
    $folder = Mage::getBaseDir('media') . DS .'import';

    if (!is_dir($folder)) {
        mkdir($folder);
    }

    $imageFilename = basename($img);
    $image_type = 'jpg'; //find the image extension
    $filename = md5($img . strtotime('now')).'.'.$image_type; //give a new name, you can modify as per your requirement
    $filepath = $folder . DS . $filename; //path for temp storage folder: ./media/import/
    $newImgUrl = file_put_contents($filepath, file_get_contents(trim($img))); //store the image from external url to the temp storage folder

    return $filepath;
}

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

@ $faker = Faker\Factory::create('pt_BR');

$store = Mage::app()->getStore();
$websiteId = Mage::app()->getWebsite()->getId();

// $timesBundle =

// $timesGrouped =


$categories = [];
$_categories = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('id')->addAttributeToSelect('is_active');

foreach ($_categories as $_category) {
    $categories[] = $_category->getId();
}

/**
 * Simple
 */

$timesSimple = !empty($argv[1]) ? $argv[1] : 1;

if ($timesSimple > 0) {
    for ($i = 0; $i < $timesSimple; $i++) {
        try {
            $product = Mage::getModel('catalog/product');

            $newsTo = null;
            $newsFrom = $faker->randomElement([null, $faker->dateTimeBetween('-60 days', '-30 days')]);

            if (!empty($newsFrom)) {
                $newsTo = $faker->dateTimeBetween('-29 days', 'now');
                $newsTo = $newsTo->format('m/d/Y');
                $newsFrom = $newsFrom->format('m/d/Y');
            }

            $specialTo = null;
            $specialFrom = $faker->randomElement([null, $faker->dateTimeBetween('-60 days', '-30 days')]);

            if (!empty($specialFrom)) {
                $specialTo = $faker->dateTimeBetween('-29 days', 'now');
                $specialTo = $specialTo->format('m/d/Y');
                $specialFrom = $specialFrom->format('m/d/Y');
            }

            $img1 = $faker->imageUrl(800, 600, 'cats');
            $img2 = $faker->imageUrl(800, 600, 'cats');
            $img3 = $faker->imageUrl(800, 600, 'cats');

            $img1 = _save_image($img1);
            $img2 = _save_image($img2);
            $img3 = _save_image($img3);

            $product
                //->setStoreId(1)
                ->setWebsiteIds(array(1))
                ->setAttributeSetId(4) // default
                ->setTypeId('simple')
                ->setCreatedAt(strtotime('now'))
                //->setUpdatedAt(strtotime('now'))
                ->setSku('simple-'. implode('-', $faker->unique()->words))
                ->setName('[SIMPLE] ' . $faker->unique()->sentence(3))
                ->setWeight($faker->randomFloat(2, 2, 10))
                ->setStatus($faker->randomElement([1, 2])) // 1 - enabled, 2 - disabled
                ->setTaxClassId($faker->randomElement([0, 1, 2, 4])) // 0 - none, 1 - default, 2 - taxable, 4 - shipping
                ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) // catalog and search visibility
                //->setManufacturer(28)
                //->setColor(24)
                ->setNewsFromDate($newsFrom)
                ->setNewsToDate($newsTo)
                ->setCountryOfManufacture('BR')

                ->setPrice($faker->randomFloat(2, 10, 400))
                ->setCost($faker->randomFloat(2, 10, 400))
                ->setSpecialPrice($faker->randomFloat(2, 10, 400))
                ->setSpecialFromDate($specialFrom)
                ->setSpecialToDate($specialTo)
                ->setMsrpEnabled($faker->randomElement([0, 1])) //enable MAP
                ->setMsrpDisplayActualPriceType($faker->randomElement([1, 2, 3, 4])) // 1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config
                ->setMsrp($faker->randomFloat(2, 10, 400)) // Manufacturer's Suggested Retail Price

                ->setMetaTitle($faker->sentence(5))
                ->setMetaKeyword(implode(' ', $faker->words(5)))
                ->setMetaDescription($faker->paragraphs(2))

                ->setDescription($faker->paragraphs(4))
                ->setShortDescription($faker->paragraphs(2))

                ->setMediaGallery(array('images' => [], 'values' => []))
                ->addImageToMediaGallery($img1, ['image', 'thumbnail', 'small_image'], false, false)
                ->addImageToMediaGallery($img2, null, false, false)
                ->addImageToMediaGallery($img3, null, false, false)

                ->setStockData([
                   'use_config_manage_stock' => $faker->randomElement([0, 1]),
                   'manage_stock' => $faker->randomElement([0, 1]),
                   'min_sale_qty' => $faker->numberBetween(1, 20),
                   'max_sale_qty' => $faker->numberBetween(21, 50),
                   'is_in_stock' => $faker->randomElement([0, 1]),
                   'qty' => $faker->numberBetween(21, 50)
                ])

                ->setCategoryIds($categories, $faker->randomDigitNotNull);

            $product->save();

        } catch (Exception $e) {
            var_dump($e->getMessage());
            continue;
        }
    }
}

/**
 * Configurable
 */

$attributes = !empty($argv[2]) ? $argv[2] : '1,color,tamanho';
$attributes = explode(',', $attributes);

$timesConfigurable = array_shift($attributes);

$attributesIds = [];
$attributesCodes = [];
$attributesOptions = [];

foreach ($attributes as $code) {
    $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $code);

    $attributesIds[] = $attribute->getId();
    $attributesCodes[] = $attribute->getCode();

    $attributesOptions[ucfirst($code)] = [];

    foreach ($attribute->getSource()->getAllOptions() as $option) {
        if (empty($option['label'])) {
            continue;
        }

        $attributesOptions[ucfirst($code)][] = $option['label'];
    }

    if (empty($attributesOptions[ucfirst($code)])) {
        unset($attributesOptions[ucfirst($code)]);
    }
}

if ($timesConfigurable > 0) {
    for ($i = 0; $i < $timesConfigurable; $i++) {
        $newsTo = null;
        $newsFrom = $faker->randomElement([null, $faker->dateTimeBetween('-60 days', '-30 days')]);

        if (!empty($newsFrom)) {
            $newsTo = $faker->dateTimeBetween('-29 days', 'now');
            $newsTo = $newsTo->format('m/d/Y');
            $newsFrom = $newsFrom->format('m/d/Y');
        }

        $specialTo = null;
        $specialFrom = $faker->randomElement([null, $faker->dateTimeBetween('-60 days', '-30 days')]);

        if (!empty($specialFrom)) {
            $specialTo = $faker->dateTimeBetween('-29 days', 'now');
            $specialTo = $specialTo->format('m/d/Y');
            $specialFrom = $specialFrom->format('m/d/Y');
        }

        $img1 = $faker->imageUrl(800, 600, 'city');
        $img2 = $faker->imageUrl(800, 600, 'city');
        $img3 = $faker->imageUrl(800, 600, 'city');

        $img1 = _save_image($img1);
        $img2 = _save_image($img2);
        $img3 = _save_image($img3);

        $productConfigurable = Mage::getModel('catalog/product');
        $productConfigurable
            ->setWebsiteIds(array(1))
            ->setAttributeSetId(4) // default
            ->setTypeId('configurable')
            ->setCreatedAt(strtotime('now'))
            ->setSku('configurable-'. implode('-', $faker->unique()->words))
            ->setName('[CONFIGURABLE] '. $faker->unique()->sentence(3))
            ->setWeight($faker->randomFloat(2, 2, 10))
            ->setStatus($faker->randomElement([1, 2])) // 1 - enabled, 2 - disabled
            ->setTaxClassId($faker->randomElement([0, 1, 2, 4])) // 0 - none, 1 - default, 2 - taxable, 4 - shipping
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) // catalog and search visibility

            ->setNewsFromDate($newsFrom)
            ->setNewsToDate($newsTo)
            ->setCountryOfManufacture('BR')

            ->setPrice($faker->randomFloat(2, 10, 400))
            ->setCost($faker->randomFloat(2, 10, 400))
            ->setSpecialPrice($faker->randomFloat(2, 10, 400))
            ->setSpecialFromDate($specialFrom)
            ->setSpecialToDate($specialTo)
            ->setMsrpEnabled($faker->randomElement([0, 1])) //enable MAP
            ->setMsrpDisplayActualPriceType($faker->randomElement([1, 2, 3, 4])) // 1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config
            ->setMsrp($faker->randomFloat(2, 10, 400)) // Manufacturer's Suggested Retail Price

            ->setMetaTitle($faker->sentence(5))
            ->setMetaKeyword(implode(' ', $faker->words(5)))
            ->setMetaDescription($faker->paragraphs(2))

            ->setDescription($faker->paragraphs(4))
            ->setShortDescription($faker->paragraphs(2))

            ->setMediaGallery(array('images' => [], 'values' => []))
            ->addImageToMediaGallery($img1, ['image', 'thumbnail', 'small_image'], false, false)
            ->addImageToMediaGallery($img2, null, false, false)
            ->addImageToMediaGallery($img3, null, false, false)

            ->setStockData([
               'use_config_manage_stock' => $faker->randomElement([0, 1]),
               'manage_stock' => $faker->randomElement([0, 1]),
               //'min_sale_qty' => $faker->numberBetween(1, 20),
               //'max_sale_qty' => $faker->numberBetween(21, 50),
               'is_in_stock' => $faker->randomElement([0, 1]),
               //'qty' => $faker->numberBetween(21, 50)
            ])

            ->setCategoryIds($categories, $faker->randomDigitNotNull);

        $productConfigurable->getTypeInstance()->setUsedProductAttributeIds($_attributesIds);

        $flatten = function ( & $combines) use ( & $flatten) {
            $current = pos($combines);
            $currentKey = key($combines);

            if (!next($combines)) {
                return end($combines);
            }

            $nextKey = key($combines);

            $temporary = $flatten($combines);

            $return = [];

            foreach ($current as $v) {
                foreach ($temporary as $t) {
                    $return[] = is_array($t) ? array_merge([$currentKey => $v], $t) : [$currentKey => $v, $nextKey => $t];
                }
            }

            next($combines);

            return $return;
        };

        $flattenAttributesOptions = $flatten($attributesOptions);
        $flattenAttributesOptions = count($flattenAttributesOptions) > 5 ? array_slice($flattenAttributesOptions, 0, 5) : $flattenAttributesOptions;

        $configurableProductsData = [];
        $configurableAttributesData = [];

        foreach ($flattenAttributesOptions as $options) {
            try {
                $simpleProductData = [];
                $simpleAttributeData = [];

                $productSimple4Configurable = Mage::getModel('catalog/product');

                $newsTo = null;
                $newsFrom = $faker->randomElement([null, $faker->dateTimeBetween('-60 days', '-30 days')]);

                if (!empty($newsFrom)) {
                    $newsTo = $faker->dateTimeBetween('-29 days', 'now');
                    $newsTo = $newsTo->format('m/d/Y');
                    $newsFrom = $newsFrom->format('m/d/Y');
                }

                $specialTo = null;
                $specialFrom = $faker->randomElement([null, $faker->dateTimeBetween('-60 days', '-30 days')]);

                if (!empty($specialFrom)) {
                    $specialTo = $faker->dateTimeBetween('-29 days', 'now');
                    $specialTo = $specialTo->format('m/d/Y');
                    $specialFrom = $specialFrom->format('m/d/Y');
                }

                $img1 = $faker->imageUrl(800, 600, 'people');
                $img2 = $faker->imageUrl(800, 600, 'people');
                $img3 = $faker->imageUrl(800, 600, 'people');

                $img1 = _save_image($img1);
                $img2 = _save_image($img2);
                $img3 = _save_image($img3);

                $productSimple4Configurable
                    ->setWebsiteIds(array(1))
                    ->setAttributeSetId(4) // default
                    ->setTypeId('simple')
                    ->setCreatedAt(strtotime('now'))
                    ->setSku('simple4configurable-'. implode('-', $faker->unique()->words))
                    ->setName('[SIMPLE4CONFIGURABLE] '. $faker->unique()->sentence(3))
                    ->setWeight($faker->randomFloat(2, 2, 10))
                    ->setStatus($faker->randomElement([1, 2])) // 1 - enabled, 2 - disabled
                    ->setTaxClassId($faker->randomElement([0, 1, 2, 4])) // 0 - none, 1 - default, 2 - taxable, 4 - shipping
                    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) // catalog and search visibility

                    ->setNewsFromDate($newsFrom)
                    ->setNewsToDate($newsTo)
                    ->setCountryOfManufacture('BR')

                    ->setPrice($faker->randomFloat(2, 10, 400))
                    ->setCost($faker->randomFloat(2, 10, 400))
                    ->setSpecialPrice($faker->randomFloat(2, 10, 400))
                    ->setSpecialFromDate($specialFrom)
                    ->setSpecialToDate($specialTo)
                    ->setMsrpEnabled($faker->randomElement([0, 1])) //enable MAP
                    ->setMsrpDisplayActualPriceType($faker->randomElement([1, 2, 3, 4])) // 1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config
                    ->setMsrp($faker->randomFloat(2, 10, 400)) // Manufacturer's Suggested Retail Price

                    ->setMetaTitle($faker->sentence(5))
                    ->setMetaKeyword(implode(' ', $faker->words(5)))
                    ->setMetaDescription($faker->paragraphs(2))

                    ->setDescription($faker->paragraphs(4))
                    ->setShortDescription($faker->paragraphs(2))

                    ->setMediaGallery(array('images' => [], 'values' => []))
                    ->addImageToMediaGallery($img1, ['image', 'thumbnail', 'small_image'], false, false)
                    ->addImageToMediaGallery($img2, null, false, false)
                    ->addImageToMediaGallery($img3, null, false, false)

                    ->setStockData([
                       'use_config_manage_stock' => $faker->randomElement([0, 1]),
                       'manage_stock' => $faker->randomElement([0, 1]),
                       'min_sale_qty' => $faker->numberBetween(1, 20),
                       'max_sale_qty' => $faker->numberBetween(21, 50),
                       'is_in_stock' => $faker->randomElement([0, 1]),
                       'qty' => $faker->numberBetween(21, 50)
                    ])

                    ->setCategoryIds($categories, $faker->randomDigitNotNull);

                foreach ($options as $key => $option) {
                    $code = strtolower($key);
                    $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $code);
                    $allOptions = $attribute->getSource()->getAllOptions();

                    $valueIndex = null;
                    foreach ($allOptions as $opt) {
                        if ($opt['label'] == $option) {
                            $valueIndex = $opt['value'];
                        }
                    }

                    if (empty($valueIndex)) {
                        continue;
                    }

                    $simpleProductData[] = [
                        'attribute_id' => $attribute->getId(),
                        'label' => $option,
                        'is_percent' => 0,
                        'pricing_value' => '',
                        'value_index' => $valueIndex
                    ];

                    $productSimple4Configurable->{'set'.$key}($option);
                }

                $productSimple4Configurable->save();
                $configurableProductsData[$productSimple4Configurable->getId()] = $simpleProductData;
            } catch (Exception $e) {
                var_dump($e->getMessage());
                continue;
            }
        }

        $productConfigurable->setConfigurableProductsData($configurableProductsData);
        $productConfigurable->setCanSaveConfigurableAttributes(true);

        $productConfigurable->save();
    }
}
