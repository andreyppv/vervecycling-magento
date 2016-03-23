<?php

class MagicToolbox_Magic360_Model_Observer {

    public function __construct() {

    }

    public function checkForMagic360Product($observer) {
        $helper = Mage::helper('magic360/settings');
        if($helper->isModuleOutputEnabled()) {
            $id = $observer->getEvent()->getProduct()->getId();
            //$gallery = $product->getMediaGalleryImages();
            //$imagesCount = $gallery->getSize();
            ////NOTE: for old Magento ver. 1.3.x
            //if(is_null($imagesCount)) {
            //    $imagesCount = count($gallery->getItems());
            //}
            $tool = $helper->loadTool('product');
            $images = array();
            $imagesCount = 0;
            $resource = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_write');
            $table = $resource->getTableName('magic360/gallery');
            $result = $connection->query("SELECT columns, gallery FROM {$table} WHERE product_id = {$id}");
            if($result) {
                $rows = $result->fetch(PDO::FETCH_ASSOC);
                if($rows) {
                    $_images = Mage::helper('core')->jsonDecode($rows['gallery']);
                    foreach($_images as $image) {
                        if($image['disabled']) continue;
                        $images[] = array(
                            'url' => $image['url'],
                            'file' => $image['file']
                        );
                    }
                    $imagesCount = count($images);
                    $tool->params->setValue('columns', $rows['columns'], $tool->params->generalProfile);
                    $tool->params->setValue('columns', $rows['columns'], 'product');
                }
            }
            if($tool->isEnabled($imagesCount, $id)) {
                Mage::register('magic360ClassName', 'magic360');
                Mage::register('magic360Images', $images);
            } else {
                Mage::register('magic360ClassName', false);
            }
        }
    }

    /*public function controller_action_predispatch($observer) {

    }*/

    /*public function beforeLoadLayout($observer) {

    }*/

    public function fixLayoutUpdates($observer) {
        //NOTE: to prevent an override of our templates with other modules

        //replace node to prevent dublicate
        //NOTE: SimpleXMLElement creates a node instead of empty values, so we use fake file name
        $child = new Varien_Simplexml_Element('<magic360 module="MagicToolbox_Magic360"><file>magictoolbox.xml</file></magic360>');
        Mage::app()->getConfig()->getNode('frontend/layout/updates')->extendChild($child, true);
        //add new node to the end
        $child = new Varien_Simplexml_Element('<magic360_layout_update module="MagicToolbox_Magic360"><file>magic360.xml</file></magic360_layout_update>');
        Mage::app()->getConfig()->getNode('frontend/layout/updates')->appendChild($child);
    }

    /*public function controller_action_postdispatch($observer) {

    }*/

    public function saveProductImagesData($observer) {
        try {
            $data = Mage::app()->getRequest()->getPost('magic360');
            if($data) {
                $images = Mage::helper('core')->jsonDecode($data['gallery']);
                $images_to_save = array();
                foreach($images as &$image) {
                    if($image['removed']) {
                        $file = str_replace('/', DS, $image['file']);
                        if(substr($file, 0, 1) == DS) {
                            $file = substr($file, 1);
                        }
                        $file = Mage::getBaseDir('media').DS.'magictoolbox'.DS.'magic360'.DS.$file;
                        @unlink($file);
                    } else {
                        $images_to_save[] = $image;
                    }
                }
                $columns = count($images_to_save);
                if(!empty($data['columns']) && $data['columns'] < $columns) {
                    $columns = $data['columns'];
                }
                $compare = create_function('$a,$b', 'if($a["position"] == $b["position"]) return 0; return (int)$a["position"] > (int)$b["position"] ? 1 : -1;');
                usort($images_to_save, $compare);
                $data = Mage::helper('core')->jsonEncode($images_to_save);

                $lengthLimit = 5000;
                $dataParts = array();
                $dataLength = strlen($data);
                while($dataLength > $lengthLimit) {
                    $dataParts[] = substr($data, 0, $lengthLimit);
                    $data = substr($data, $lengthLimit);
                    $dataLength = strlen($data);
                }
                $dataParts[] = $data;

                $id = $observer->getEvent()->getProduct()->getId();
                $resource = Mage::getSingleton('core/resource');
                $connection = $resource->getConnection('core_write');
                $table = $resource->getTableName('magic360/gallery');
                $result = $connection->query("SELECT product_id FROM {$table} WHERE product_id = {$id}");
                if($result) {
                    $rows = $result->fetch(PDO::FETCH_ASSOC);
                    if($rows) {
                        if(empty($images_to_save)) {
                            $connection->query("DELETE FROM {$table} WHERE product_id = {$id}");
                        } else {
                            //$connection->query("UPDATE {$table} SET columns = {$columns}, gallery = '{$data}' WHERE product_id = {$id}");
                            $query = "UPDATE {$table} SET columns = {$columns}, gallery = '{$dataParts[0]}' WHERE product_id = {$id}";
                            $connection->query($query);
                            unset($dataParts[0]);
                            if(count($dataParts)) {
                                foreach($dataParts as $dataPart) {
                                    $query = "UPDATE {$table} SET gallery = concat(gallery, '{$dataPart}') WHERE product_id = {$id}";
                                    $connection->query($query);
                                }
                            }
                        }
                    } else {
                        if(!empty($images_to_save)) {
                            //$connection->query("INSERT INTO {$table} (product_id, columns, gallery) VALUES ({$id}, {$columns}, '{$data}')");
                            $query = "INSERT INTO {$table} (product_id, columns, gallery) VALUES ({$id}, {$columns}, '{$dataParts[0]}')";
                            $connection->query($query);
                            unset($dataParts[0]);
                            if(count($dataParts)) {
                                foreach($dataParts as $dataPart) {
                                    $query = "UPDATE {$table} SET gallery = concat(gallery, '{$dataPart}') WHERE product_id = {$id}";
                                    $connection->query($query);
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

}

?>