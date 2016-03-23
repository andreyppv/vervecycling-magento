<?php

class Infinity_SupportSwitcher_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function parseLocationsOption()
    {
        $finalLocationList = array();
        $locationsListOption = Mage::getStoreConfig('design/header/supportswitcher', Mage::app()->getStore());
        $tempList = explode(',', $locationsListOption);
        if(empty($tempList)) return $this->getDefaultList();
        foreach($tempList as $item){
            $item = explode(':', $item);
            $finalLocationList[strtolower(trim($item[0]))] = array(
                'title'    => trim($item[0]),
                'subtitle' => trim($item[1]),
                'phone'    => trim($item[2]),
            );
        }
        return empty($finalLocationList)? $this->getDefaultList() : $finalLocationList;
    }

    public function getDefaultList()
    {
        return array(
            'us' => array(
                'title' => 'United States',
                'subtitle' => 'US Support',
                'phone' => '+1 510-298-3783'
            ),
            'au' => array(
                'title' => 'Australia',
                'subtitle' => 'Australia Support',
                'phone' => '+61 449 999 432'
            ),
            'eu' => array(
                'title' => 'Europe',
                'subtitle' => 'EU Support',
                'phone' => '+44 131 510 2983'
            )
        );
    }
}