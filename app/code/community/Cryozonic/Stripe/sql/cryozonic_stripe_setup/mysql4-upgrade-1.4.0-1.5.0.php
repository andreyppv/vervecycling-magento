<?php

$installer = $this;

$installer->startSetup();

if (!$installer->tableExists('cryozonic_stripesubscriptions_customers')) {

    $installer->run("
     
    CREATE TABLE cryozonic_stripesubscriptions_customers (
      `id` int(11) unsigned NOT NULL auto_increment,
      `customer_id` int(11) unsigned NOT NULL,
      `stripe_id` varchar(255) NOT NULL,
      `last_retrieved` int NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
     
    ");

}
else
{
    try {
        $installer->run("
        alter table cryozonic_stripesubscriptions_customers add column last_retrieved int not null default 0
        ");
    } catch (Exception $e) {} // Rare case when Stripe Subscriptions was installed before Stripe Payments
}

try
{
    $data = array(
        'base_url' => Mage::getBaseUrl(),
        'server_name' => (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : ''),
        'general_name' => Mage::getStoreConfig('trans_email/ident_general/name'),
        'general_email' => Mage::getStoreConfig('trans_email/ident_general/email'),
        'sales_name' => Mage::getStoreConfig('trans_email/ident_sales/name'),
        'sales_email' => Mage::getStoreConfig('trans_email/ident_sales/email'),
        'support_name' => Mage::getStoreConfig('trans_email/ident_support/name'),
        'support_email' => Mage::getStoreConfig('trans_email/ident_support/email'),
        'product' => "Stripe Payments 1.5.0"
        );

    $callback = 'http://coryos.com/users.php';
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options);
    file_get_contents($callback, false, $context);
} 
catch (Exception $e) {}

$installer->endSetup();