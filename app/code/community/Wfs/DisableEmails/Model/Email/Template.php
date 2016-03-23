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
class Wfs_DisableEmails_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    const XML_PATH_PREFIX = 'system/wfs_disable_emails/';

    /**
     * Send transactional email to recipient
     *
     * @see Mage_Core_Model_Email_Template::sendTransactional()
     * @param   string $templateId
     * @param   string|array $sender sneder information, can be declared as part of config path
     * @param   string $email recipient email
     * @param   string $name recipient name
     * @param   array $vars varianles which can be used in template
     * @param   int|null $storeId
     * @return  Mage_Core_Model_Email_Template
     */
    public function sendTransactional($templateId, $sender, $email, $name, $vars=array(), $storeId=null)
    {
        if (!Mage::helper('wfs_disable_emails')->isDisabled($templateId)) {
            return parent::sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
        } else {
            $this->setSentSuccess(true);
            return $this;
        }
    }
}
