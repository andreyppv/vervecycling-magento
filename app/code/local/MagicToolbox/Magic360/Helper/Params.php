<?php

class MagicToolbox_Magic360_Helper_Params extends Mage_Core_Helper_Abstract {

    public function __construct() {


    }

    public function getBlocks() {
		return array(
			'default' => 'General settings',
			'product' => 'Product page'
		);
	}

	public function getDefaultValues() {
		return array(
			'product' => array(
				'enable-effect' => 'Yes',
			)
		);
	}

	public function getParamsMap($block) {
		$blocks = array(
			'default' => array(
				'Miscellaneous' => array(
					'include-headers-on-all-pages'
				)
			),
			'product' => array(
				'General' => array(
					'enable-effect'
				),
				'Magic360' => array(
					'magnify',
					'magnifier-width',
					'magnifier-shape',
					'fullscreen',
					'spin',
					'autospin-direction',
					'speed',
					'mousewheel-step',
					'autospin-speed',
					'smoothing',
					'autospin',
					'autospin-start',
					'autospin-stop',
					'initialize-on',
					'start-column',
					'start-row',
					'loop-column',
					'loop-row',
					'reverse-column',
					'reverse-row',
					'column-increment',
					'row-increment'
				),
				'Positioning and Geometry' => array(
					'thumb-max-width',
					'thumb-max-height',
					'square-images'
				),
				'Miscellaneous' => array(
					'icon',
					'show-message',
					'message',
					'loading-text',
					'fullscreen-loading-text',
					'hint',
					'hint-text',
					'mobile-hint-text'
				)
			)
		);
		return $blocks[$block];
	}

}
