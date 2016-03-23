<?php
/**
 * WebFlakeStudio
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webflakestudio.com/WFS-LICENSE-COMMUNITY.txt
 *
 *
 * MAGENTO EDITION USAGE NOTICE
 *
 * This package designed for Magento COMMUNITY edition
 * WebFlakeStudio does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * WebFlakeStudio does not provide extension support in case of
 * incorrect edition usage.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *
 * @category   Wfs
 * @package    Wfs_DisableEmails
 * @copyright  Copyright (c) 2012 WebFlakeStudio (http://webflakestudio.com)
 * @license    http://webflakestudio.com/WFS-LICENSE-COMMUNITY.txt
 */
class Wfs_DisableEmails_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check if given email template is disabled
     *
     * @param string $templateId
     * @return boolean
     */
    public function isDisabled($templateId)
    {
        $path = Wfs_DisableEmails_Model_Email_Template::XML_PATH_PREFIX . $templateId;
        return '1' === Mage::getStoreConfig($path);
    }
}
