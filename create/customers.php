<?php

require __DIR__ . '/../../../app/Mage.php';
require __DIR__ . '/../vendor/autoload.php';

@ $faker = Faker\Factory::create('pt_BR');

$store = Mage::app()->getStore();
$websiteId = Mage::app()->getWebsite()->getId();

$times = !empty($argv[1]) ? $argv[1] : 1;

for ($i = 0; $i < $times; $i++) {
    $customer = Mage::getModel("customer/customer");
    $customer->setWebsiteId($websiteId)
        ->setStore($store)
        ->setFirstname($faker->unique()->firstName())
        ->setLastname($faker->lastName)
        ->setEmail($faker->email)
        ->setPassword('123456');

    try {
        $customer->save();
    } catch (Exception $e) {
        var_dump($e->getMessage());
        continue;
    }

    $address = Mage::getModel("customer/address");
    $address->setCustomerId($customer->getId())
        ->setFirstname($customer->getFirstname())
        ->setLastname($customer->getLastname())
        ->setCountryId('BR')
        ->setPostcode($faker->unique()->postcode)
        ->setCity($faker->unique()->city)
        ->setTelephone($faker->unique()->phoneNumber)
        ->setFax($faker->unique()->phoneNumber)
        ->setCompany($faker->unique()->company)
        ->setStreet($faker->unique()->streetName)
        ->setIsDefaultBilling(1)
        ->setIsDefaultShipping(1)
        ->setSaveInAddressBook(1);

    try {
        $address->save();
    } catch (Exception $e) {
        var_dump($e->getMessage());
        continue;
    }
}


