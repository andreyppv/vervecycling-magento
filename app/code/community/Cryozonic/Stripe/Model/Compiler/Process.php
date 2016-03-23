<?php
/**
 * Cryozonic
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Single Domain License
 * that is available through the world-wide-web at this URL:
 * http://cryozonic.com/licenses/stripe.html
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to info@cryozonic.com so we can send
 * you a copy immediately.
 *
 * @category   Cryozonic
 * @package    Cryozonic_Stripe
 * @copyright  Copyright (c) Cryozonic Ltd (http://cryozonic.com)
 */

class Cryozonic_Stripe_Model_Compiler_Process extends Mage_Compiler_Model_Process
{
    protected function _copyAll($source, $target)
    {
        if (is_dir($source)) {
            $this->_mkdir($target);
            $dir = dir($source);
            while (false !== ($file = $dir->read())) {
                if (($file[0] == '.')) {
                    continue;
                }
                $sourceFile = $source . DS . $file;
                $targetFile = $target . DS . $file;
                $this->_copyAll($sourceFile, $targetFile);
            }
        } else {
            if (!in_array(substr($source, strlen($source)-4, 4), array('.php','.crt'))) {
                return $this;
            }
            copy($source, $target);
        }
        return $this;
    }
}