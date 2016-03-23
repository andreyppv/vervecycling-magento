<?php
 class Verve_Cycling_Block_Review_Helper extends Mage_Review_Block_Helper {
     protected $_availableTemplates = array(
         'default' => 'review/helper/summary.phtml',
         'short'   => 'review/helper/summary_short.phtml',
         'view' => 'review/helper/summary_view.phtml'
     );
 }