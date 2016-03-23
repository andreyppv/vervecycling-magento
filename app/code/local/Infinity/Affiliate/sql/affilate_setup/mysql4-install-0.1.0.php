<?php
$installer = $this;
$installer->startSetup();

$attributeSettings = array(
    'type'          => 'varchar',
    'label'         => 'Affiliate Partner Name',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => true,
    'user_defined'  =>  true
);

$installer->addAttribute("quote", "affiliate_partner_name", $attributeSettings);
$installer->addAttribute("order", "affiliate_partner_name", $attributeSettings);


$installer->endSetup();
	 