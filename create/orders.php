<?php

require __DIR__ . '/../../../app/Mage.php';
require __DIR__ . '/../vendor/autoload.php';

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
@ $faker = Faker\Factory::create('pt_BR');

$_paymentCodes = [];
$payments = Mage::getSingleton('payment/config')->getActiveMethods();
foreach ($payments as $key => $value) {
    if (!in_array($key, ['checkmo'])) {
        continue;
    }

    $_paymentCodes[] = $key;
}

$_shippingCodes = [];
$shipping = Mage::getSingleton('shipping/config')->getActiveCarriers();
foreach ($shipping as $key => $value) {
    if (!in_array($key, ['flatrate', 'freeshipping'])) {
        continue;
    }

    $_shippingCodes[] = $key;
}

if (empty($_paymentCodes)) {
    throw new Exception("Precisamos de uma forma de pagamento valida ativa");
}

if (empty($_shippingCodes)) {
    throw new Exception("Precisamos de uma forma de envio valida ativa");
}

$times = empty($argv[1]) ? $faker->numberBetween(1, 5) : $argv[1];

for ($i = 0; $i < $times; $i++) {
    try {
        $quote = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore('default')->getId());

        // orders de customers ?
        if (true) {
            $_customersEmails = [];
            $customers = mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('email');

            foreach ($customers as $customer) {
                $_customersEmails[] = $customer->getEmail();
            }

            $customer = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($faker->randomElement($_customersEmails));
            $quote->assignCustomer($customer);
        } else {
            // for guesr orders only:
            //$quote->setCustomerEmail('customer@example.com');
        }

        $numberOfProducts = $faker->numberBetween(2, 20);
        
        $products = mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('id')
            >setPageSize($numberOfProducts)
            ->setCurPage(1);

        $_productsIds = [];
        foreach ($products as $product) {
            $_productsIds[] = $product->getId();
        }
        
        $productsIdsRange = range(1, $numberOfProducts);
        
        foreach ($productsIdsRange as $range) {
            $product = Mage::getModel('catalog/product')->load($faker->unique()->randomElement($_productsIds));
            $stock = $product->getStockItem();

            if (!$stock->getIsInStock()) {
                continue;
            }

            $min = (int) $stock->getMinSaleQty();
            $max = (int) min([$stock->getQty(), $stock->getMaxSaleQty()]);
            $qty = $faker->numberBetween($min, $max);

            $buyInfo = ['qty' => $qty];
            $quote->addProduct($product, new Varien_Object($buyInfo));
        }

        $faker->unique(true);

        $addressData = array(
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'street' => $faker->streetName,
            'city' => $faker->city,
            'postcode' => $faker->postcode,
            'telephone' => $faker->phoneNumber,
            'country_id' => 'BR',
            'region_id' => $faker->numberBetween(1, 12)
        );

        $billingAddress = $quote->getBillingAddress()->addData($addressData);
        $shippingAddress = $quote->getShippingAddress()->addData($addressData);

        $paymentMethod = $faker->randomElement($_paymentCodes);

        $shipping = $faker->randomElement($_shippingCodes);

        $shippingAddress->setCollectShippingRates(true)->collectShippingRates()
                        ->setShippingMethod($shipping .'_'. $shipping)
                        ->setPaymentMethod($paymentMethod);

        $quote->getPayment()->importData(array('method' => $paymentMethod));
        $quote->setBillingAddress($billingAddress);
        $quote->setShippingAddress($shippingAddress);


        $service = Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();

        $order = $service->getOrder();

        $order->setStatus($faker->randomElement([
            'pending',
            'complete',
            'canceled',
            'closed',
            'fraud',
            'holded',
            'payment_review',
            'pending_payment',
            'processing'
        ]));

        $order->save();

        printf("Created order %s\n", $order->getIncrementId());
    } catch (Exception $e) {
        var_dump($e->getMessage(), $paymentMethod, $qty);
        continue;
    }
}
