<?php
class Shipwire_Shipping_Model_Carrier_Service {
    public function toOptionArray()
    {
		return array(
			array(
				'value' => '1D',
				'label' => 'One Day Service'
			),
			array(
				'value' => '2D',
				'label' => 'Two Day Service'
			),
			array(
				'value' => 'GD',
				'label' => 'Ground Service'
			),
			array(
				'value' => 'FT',
				'label' => 'Freight Service'
			),
			array(
				'value' => 'INTL',
				'label' => 'International Service'
			),
			array(
				'value' => 'E-INTL',
				'label' => 'International Economy Service'
			),
			array(
				'value' => 'PL-INTL',
				'label' => 'International plus Service'
			),
			array(
				'value' => 'PM-INTL',
				'label' => 'International Premium Service'
			)
		);
    }
}