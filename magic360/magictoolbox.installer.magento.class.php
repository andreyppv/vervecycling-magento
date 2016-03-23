<?php
    /**
        Magento module installer class
    */

    require_once(dirname(__FILE__) . '/magictoolbox.installer.core.class.php');

    class MagicToolboxMagentoModuleInstallerClass extends MagicToolboxCoreInstallerClass {

        private $design = 'base';
        private $mageVersion;
        private $foundCopies = array();
        private $platformVersion = '';

        //NOTE: for very old modules
        private $skinDir = '/skin/frontend/default/default';
        private $themeDir = '/app/design/frontend/default/default';
        private $themeDirDefault = '/app/design/frontend/default/default';

        function MagicToolboxMagentoModuleInstallerClass() {
            $this->dir = dirname(dirname(__FILE__));
            $this->modDir = dirname(__FILE__) . '/module';
        }

        function isModuleInstalled() {
            $this->setStatus('check', 'module');
            if(file_exists($this->dir.'/app/code/local/MagicToolbox/Magic360/etc/config.xml') ||
               //NOTE: very old module
               file_exists($this->dir.'/app/etc/magictoolbox')) {
                return true;
            }
            return false;
        }

        function isModuleDisabled() {
            if(file_exists($this->dir.'/app/etc/modules/MagicToolbox_Magic360.xml')) {
                $contents = file_get_contents($this->dir.'/app/etc/modules/MagicToolbox_Magic360.xml');
                if(false === $contents) {
                    return false;
                }
                if(preg_match('/<active>true<\/active>/is', $contents)) {
                    return false;
                }
            }
            return true;
        }

        function disableModule() {
            if(file_exists($this->dir.'/app/etc/modules/MagicToolbox_Magic360.xml')) {
                $contents = file_get_contents($this->dir.'/app/etc/modules/MagicToolbox_Magic360.xml');
                if(false !== $contents) {
                    $contents = preg_replace('/<active>true<\/active>/is', '<active>false</active>', $contents);
                    file_put_contents($this->dir.'/app/etc/modules/MagicToolbox_Magic360.xml', $contents);
                }
            }
        }

        function checkPlace() {
            $this->setStatus('check', 'place');
            if(!is_dir($this->dir . '/app') && !file_exists($this->dir . '/index.php')) {
                $this->setError('Wrong location: please upload the files from the ZIP archive to the Magento store directory.');
                return false;
            }
            return true;
        }

        function prepare() {

            $canContinue = true;

            // go to magento core folder
            chdir($this->dir);
            ob_start();
            // include core magento file (load front page)
            include('index.php');
            ob_end_clean();

            if($this->installMode == 'uninstall')
            foreach(Mage::app()->getCacheInstance()->getTypes() as $type) {
                if($type->getData('status')) {
                    $this->setError('Please, disable Magento Cache before uninstalling module.');
                    $canContinue = false;
                    break;
                }
            }

            if(defined('COMPILER_INCLUDE_PATH')) {
                $this->setError('Please, disable Magento Compiler before continuing.');
                $canContinue = false;
            }

            if($canContinue && !$this->isModuleDisabled()) {
                $this->disableModule();
            }

            //check Magento version
            $pattern = "/([0-9]+\.[0-9]+\.[0-9]+)(?:\.[0-9]+)*/";
            $matches = array();
            if(preg_match($pattern, Mage::getVersion(), $matches)) {
                $this->platformVersion = $matches[0];
                $this->mageVersion = $matches[1];
                if(version_compare($this->mageVersion, '1.4.0', '<')) {
                    $this->design = 'default';
                }
            }

            $this->resDir = "/" . preg_replace('/https?:\/\/[^\/]+\//is','',str_replace('/magic360.css', '', Mage::getSingleton('core/design_package')->getSkinUrl('css/magic360/magic360.css')));
            //TODO: find better way to get web path
            $this->resDir = preg_replace('/(skin\/frontend\/)([^\/]+\/[^\/]+)/is', '$1base/default', $this->resDir);

            if($this->design == 'default') {
                $this->resDir = str_replace('/base/', '/default/', $this->resDir);
            }
            //this hack need if Web Base URL contains {{base_url}}
            $this->resDir = str_replace('/magic360/', '/', $this->resDir);


            if($canContinue && $this->isModuleDisabled()) {
                $resource = Mage::getSingleton('core/resource');
                $connection = $resource->getConnection('core_write');
                $table = $resource->getTableName('core/resource');
                $result = $connection->query("SELECT * FROM {$table} WHERE code = 'magic360_setup'");
                if($result) {
                    $rows = $result->fetch(PDO::FETCH_ASSOC);
                    if($rows) {
                        $connection->query("DELETE FROM {$table} WHERE code = 'magic360_setup'");
                    }
                }
                //delete old options
                $result = $connection->query("DROP TABLE IF EXISTS magic360");
            }

            $availableDesigns = Mage::getSingleton('core/design_source_design')->getAllOptions();
            foreach($availableDesigns as $pKey => $package) {
                if(is_array($package['value'])) {
                    foreach($package['value'] as $tKey => $theme) {
                        if($package['label'] == $this->design && $theme['label'] == 'default') continue;
                        if(file_exists($this->dir . '/app/design/frontend/'.$package['label'].'/'.$theme['label'].'/template/magic360')) {
                            $this->foundCopies[] = '/app/design/frontend/'.$package['label'].'/'.$theme['label'].'/template/magic360';
                        }
                        if(file_exists($this->dir . '/app/design/frontend/'.$package['label'].'/'.$theme['label'].'/layout/magic360.xml')) {
                            $this->foundCopies[] = '/app/design/frontend/'.$package['label'].'/'.$theme['label'].'/layout/magic360.xml';
                        }
                    }
                }
            }

            //NOTE: for very old modules
            if(file_exists($this->dir.'/app/etc/magictoolbox')) {
                // get current interface and theme
                $interface = Mage::getSingleton('core/design_package')->getPackageName();
                $theme = Mage::getSingleton('core/design_package')->getTheme('template');
                $skin = Mage::getSingleton('core/design_package')->getTheme('skin');

                // setup paths
                $this->skinDir = '/skin/frontend/' . $interface . '/' . $skin;
                $this->themeDir = '/app/design/frontend/' . $interface . '/' . $theme;
                $this->themeDirDefault = '/app/design/frontend/' . $interface . '/default';
            }

            // return to installer folder
            chdir(dirname(__FILE__));

            return $canContinue;
        }

        function checkPerm() {
            $this->setStatus('check', 'perm');
            $files = array(
                // directory
                '/app/design/adminhtml/default/default/layout',
                '/app/design/adminhtml/default/default/template',
                '/app/etc/modules',
                '/js',
                '/app/design/frontend/'.$this->design.'/default/layout',
                '/app/design/frontend/'.$this->design.'/default/template',
                '/skin/adminhtml/default/default',
                '/skin/frontend/'.$this->design.'/default/css',
                //DEPRECATED:
                //'/skin/frontend/'.$this->design.'/default/images',
                '/skin/frontend/'.$this->design.'/default/js',
            );

            if(file_exists($this->dir.'/media/magictoolbox/magic360')) {
                $files[] = '/media/magictoolbox/magic360';
            } else if(file_exists($this->dir.'/media/magictoolbox')) {
                $files[] = '/media/magictoolbox';
            } else {
                $files[] = '/media';
            }

            if(file_exists($this->dir.'/skin/frontend/'.$this->design.'/default/css/magic360')) {
                $files[] = '/skin/frontend/'.$this->design.'/default/css/magic360';
            }

            if(file_exists($this->dir . '/app/code/local')) {
                $files[] = '/app/code/local';
            } else {
                $files[] = '/app/code';
            }

            $excludeDesign = ($this->design == 'base')?'default':'base';
            foreach($this->getFilesRecursive($this->modDir) as $file) {
                if(strpos($file, '/app/design/frontend/'.$excludeDesign) === 0) continue;
                if(strpos($file, '/skin/frontend/'.$excludeDesign) === 0) continue;
                if(file_exists($this->dir . $file)) {
                    $files[] = $file;
                }
            }

            if(file_exists($this->dir . '/app/etc/modules/MagicToolbox_Magic360.xml')) {
                $files[] = '/app/etc/modules/MagicToolbox_Magic360.xml';
            }

            list($result, $wrang) = $this->checkFilesPerm($files);
            if(!$result) {
                $this->setError('This installer need to modify some Magento store files.');
                $this->setError('Please check write access for following files and/or dirrectories of your Magento store:');
                $this->setError(array_unique($wrang), '&nbsp;&nbsp;&nbsp;-&nbsp;');
                return false;
            }
            return true;
        }

        function getFilesRecursive($path, $firstCall = true) {
            $result = array();
            $files = glob($path . '/*');
            if($files !== false) {
                foreach($files as $file) {
                    if(is_dir($file)) {
                        $result = array_merge($result, $this->getFilesRecursive($file, false));
                    } else {
                        $result[] = $file;
                    }
                }
            }
            if($firstCall) {
                $result = str_replace($path, '', $result);
            }
            return $result;
        }

        function installFiles() {
            $this->setStatus('install', 'files');

            //create local folder is it does not exists
            if(!file_exists($this->dir . '/app/code/local')) {
                $this->createDirRecursive($this->dir . '/app/code/local');
            }

            //copy app, js, skin folders
            $this->copyDir($this->modDir . '/app/code', $this->dir . '/app/code');
            $this->copyDir($this->modDir . '/app/design/adminhtml', $this->dir . '/app/design/adminhtml');
            $this->copyDir($this->modDir . '/app/design/frontend/'.$this->design, $this->dir . '/app/design/frontend/'.$this->design);
            $this->copyDir($this->modDir . '/js', $this->dir . '/js');
            $this->copyDir($this->modDir . '/skin/adminhtml', $this->dir . '/skin/adminhtml');
            $this->copyDir($this->modDir . '/skin/frontend/'.$this->design, $this->dir . '/skin/frontend/'.$this->design);
            $this->copyDir($this->modDir . '/media', $this->dir . '/media');

            //modify config.xml
            if(isset($this->mageVersion) && version_compare($this->mageVersion, '1.4.1', '<')) {
                $fileContents = file_get_contents($this->dir . '/app/code/local/MagicToolbox/Magic360/etc/config.xml');
                $rCount = 0;
                $fileContents = preg_replace('/<!--(<page>.*?MagicToolbox_Magic360_Block_Html_Head.*?<\/page>)-->/is', '$1', $fileContents, 1, $rCount);
                if($rCount) {
                    file_put_contents($this->dir . '/app/code/local/MagicToolbox/Magic360/etc/config.xml', $fileContents);
                }
            }

            //this must be last
            $this->copyDir($this->modDir . '/app/etc', $this->dir . '/app/etc');

            if(count($this->foundCopies)) {
                $this->setError('The following layout and/or template files was detected in your Magento directory:');
                $this->setError($this->foundCopies, '&nbsp;&nbsp;&nbsp;-&nbsp;');
                $this->setError('Make sure to update these files from \''.$this->design.'/default\' design if needed!');
            }

            return true;
        }

        function restoreStep_installFiles() {

            //NOTE: for very old modules
            if(file_exists($this->dir.'/app/etc/magictoolbox')) {
                return $this->restoreStep_installFiles_old();
            }

            @unlink($this->dir . '/app/etc/modules/MagicToolbox_Magic360.xml');

            $this->removeDir($this->dir . '/app/code/local/MagicToolbox/Magic360');
            if($this->isEmptyDir($this->dir . '/app/code/local/MagicToolbox')) {
                $this->removeDir($this->dir . '/app/code/local/MagicToolbox');
                $removeAll = true;
            } else $removeAll = false;

            unlink($this->dir . '/app/design/adminhtml/default/default/layout/magic360.xml');
            $this->removeDir($this->dir . '/app/design/adminhtml/default/default/template/magic360');

            unlink($this->dir . '/app/design/frontend/'.$this->design.'/default/layout/magic360.xml');
            $this->removeDir($this->dir . '/app/design/frontend/'.$this->design.'/default/template/magic360');
            if(file_exists($this->dir.'/skin/frontend/'.$this->design.'/default/css/magic360.css')) {
                unlink($this->dir.'/skin/frontend/'.$this->design.'/default/css/magic360.css');
            } else if(file_exists($this->dir.'/skin/frontend/'.$this->design.'/default/css/magic360')) {
                $this->removeDir($this->dir.'/skin/frontend/'.$this->design.'/default/css/magic360');
            }
            unlink($this->dir . '/skin/frontend/'.$this->design.'/default/js/magic360.js');

            $this->removeDir($this->dir . '/js/magic360');
            $this->removeDir($this->dir . '/skin/adminhtml/default/default/magic360');
            if($removeAll) {
                //DEPRECATED:
                //if(file_exists($this->dir . '/skin/frontend/'.$this->design.'/default/images/loader.gif'))
                //    unlink($this->dir . '/skin/frontend/'.$this->design.'/default/images/loader.gif');
            }

            return true;
        }

        function restoreStep_installFiles_old() {
            $files = array(
                $this->themeDir . '/template/catalog/product/view/media.phtml',
                $this->themeDir . '/template/page/html/head.phtml',
            );
            $this->restoreFromBackups($files);
            $this->removeDir($this->dir . '/app/etc/magictoolbox');
            unlink($this->dir . $this->themeDir . '/template/catalog/product/view/media_original.phtml');
            unlink($this->dir.$this->skinDir.'/css/magic360.css');
            $this->removeDir($this->dir.$this->skinDir.'/css/graphics');
            unlink($this->dir.$this->skinDir.'/js/magic360.js');
            unlink($this->dir.$this->skinDir.'/js/magictoolbox_utils.js');
            return true;
        }

        function uninstall_from_logFile($exclude = array()) {
            return parent::uninstall_from_logFile($exclude);
        }

        function upgrade($files) {
            $path = $this->dir . '/skin/frontend/'.$this->design.'/default/js/';
            foreach($files as $name => $file) {
                if(file_exists($path . $name)) {
                    unlink($path . $name);
                }
                file_put_contents($path . $name, $file);
                chmod($path . $name, 0755);
            }
            return true;
        }

        function getPlatformVersion() {
            return $this->platformVersion;
        }


    }

?>