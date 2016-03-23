<?php

if(!defined('Magic360ModuleCoreClassLoaded')) {

    define('Magic360ModuleCoreClassLoaded', true);

    require_once(dirname(__FILE__) . '/magictoolbox.params.class.php');

    /**
     * Magic360ModuleCoreClass
     *
     */
    class Magic360ModuleCoreClass {

        /**
         * MagicToolboxParamsClass class
         *
         * @var   MagicToolboxParamsClass
         *
         */
        var $params;

        /**
         * Tool type
         *
         * @var   string
         *
         */
        var $type = 'circle';

        /**
         * Constructor
         *
         * @return void
         */
        function Magic360ModuleCoreClass() {
            $this->params = new MagicToolboxParamsClass();
            $this->loadDefaults();
            $this->params->setMapping(array(
                'smoothing' => array('Yes' => 'true', 'No' => 'false'),
                'magnify' => array('Yes' => 'true', 'No' => 'false'),
                'loop-column' => array('Yes' => 'true', 'No' => 'false'),
                'loop-row' => array('Yes' => 'true', 'No' => 'false'),
                'reverse-column' => array('Yes' => 'true', 'No' => 'false'),
                'reverse-row' => array('Yes' => 'true', 'No' => 'false'),
                //'start-column' => array('auto' => '\'auto\''),
                //'start-row' => array('auto' => '\'auto\''),
                'fullscreen' => array('Yes' => 'true', 'No' => 'false'),
                'hint' => array('Yes' => 'true', 'No' => 'false'),
            ));
        }

        /**
         * Metod to get headers string
         *
         * @param string $jsPath  Path to JS file
         * @param string $cssPath Path to CSS file
         *
         * @return string
         */
        function getHeadersTemplate($jsPath = '', $cssPath = null) {
            //to prevent multiple displaying of headers
            if(!defined('Magic360ModuleHeaders')) {
                define('Magic360ModuleHeaders', true);
            } else {
                return '';
            }
            if($cssPath == null) $cssPath = $jsPath;
            $headers = array();
            // add module version
            $headers[] = '<!-- Magic 360 Magento module version v4.8.5 [v1.3.27:v4.5.3] -->';
            // add style link
            $headers[] = '<link type="text/css" href="' . $cssPath . '/magic360.css" rel="stylesheet" media="screen" />';
            // add script link
            $headers[] = '<script type="text/javascript" src="' . $jsPath . '/magic360.js"></script>';
            // add options
            $headers[] = $this->getOptionsTemplate();
            return "\r\n" . implode("\r\n", $headers) . "\r\n";
        }

        /**
         * Metod to get options string
         *
         * @return string
         */
        function getOptionsTemplate() {
            $addition = '';
            if($this->params->paramExists('rows')) {
                $addition .= "\n\t\t'rows':" . $this->params->getValue('rows') . ",";
            } else {
                $addition .= "\n\t\t'rows':1,";
            }
            return "<script type=\"text/javascript\">\n\tMagic360.options = {{$addition}\n\t\t".$this->params->serialize(true, ",\n\t\t")."\n\t}\n</script>\n" .
                   "<script type=\"text/javascript\">\n\tMagic360.lang = {" .
                   "\n\t\t'loading-text':'".str_replace('\'', '\\\'', $this->params->getValue('loading-text'))."',".
                   "\n\t\t'fullscreen-loading-text':'".str_replace('\'', '\\\'', $this->params->getValue('fullscreen-loading-text'))."',".
                   "\n\t\t'hint-text':'".str_replace('\'', '\\\'', $this->params->getValue('hint-text'))."',".
                   "\n\t\t'mobile-hint-text':'".str_replace('\'', '\\\'', $this->params->getValue('mobile-hint-text'))."',".
                   "\n\t}\n</script>";
        }

        /**
         * Check if effect is enable
         *
         * @param mixed $data Images Data
         * @param mixed $id Product ID
         *
         * @return boolean
         */
        function isEnabled($data, $id) {
            if(intval($this->params->getValue('columns')) == 0) {
                return false;
            }
            if(is_array($data)) $data = count($data);
            if($data < intval($this->params->getValue('columns'))) {
                return false;
            }
            $ids = trim($this->params->getValue('product-ids'));
            if($ids != 'all' && !in_array($id, explode(',', $ids))) {
                return false;
            }
            return true;
        }

        /**
         * Metod to get Magic360 HTML
         *
         * @param array $data Magic360Flash Data
         * @param array $params Additional params
         *
         * @return string
         */
        function getMainTemplate($data, $params = array()) {

            $id = '';
            $width = '';
            $height = '';

            $html = array();

            extract($params);

            // check for width/height
            if(empty($width)) $width = ''; else $width = " width=\"{$width}\"";
            if(empty($height)) $height = ''; else $height = " height=\"{$height}\"";

            // check ID
            if(empty($id)) {
                $id = '';
            } else {
                $id = ' id="' . addslashes($id) . '"';
            }

            $images = array();// set of small images
            $largeImages = array();// set of large images

            $first = reset($data);
            $src = ' src="' . $first['medium'] . '"';

            // add items
            foreach($data as $item) {
                //NOTE: if there are spaces in the filename
                $images[] = str_replace(' ', '%20', $item['medium']);
                $largeImages[] = str_replace(' ', '%20', $item['img']);
            }

            $rel = $this->params->serialize();
            $rel .= 'rows:' . floor(count($data)/$this->params->getValue('columns')) . ';';
            $rel .= 'images:' . implode(' ', $images) . ';';
            if($this->params->checkValue('magnify', 'Yes') || $this->params->checkValue('fullscreen', 'Yes')) {
                $rel .= 'large-images:' . implode(' ', $largeImages) . ';';
            }
            $rel = ' data-magic360-options="'.$rel.'"';

            $html[] = '<a' . $id . ' class="Magic360" href="#"' . $rel . '>';
            $html[] = '<img itemprop="image"' . $src . $width . $height . ' />';
            $html[] = '</a>';

            // check message
            if($this->params->checkValue('show-message', 'Yes')) {
                // add message
                $html[] = '<div class="MagicToolboxMessage">' . $this->params->getValue('message') . '</div>';
            }

            // return HTML string
            return implode('', $html);
        }

        /**
         * Metod to load defaults options
         *
         * @return void
         */
        function loadDefaults() {
            $params = array("enable-effect"=>array("id"=>"enable-effect","group"=>"General","order"=>"10","default"=>"Yes","label"=>"Enable Magic 360™","type"=>"array","subType"=>"select","values"=>array("Yes","No")),"product-ids"=>array("id"=>"product-ids","group"=>"Magic360","order"=>"40","default"=>"all","label"=>"Product IDs (all = all products have 360)","description"=>"Choose which products has 360 images (comma separated, e.g. 1,4,5,12,14)","type"=>"text"),"columns"=>array("id"=>"columns","group"=>"Magic360","order"=>"50","default"=>"36","label"=>"Number of images on X-axis","type"=>"num","scope"=>"tool"),"magnify"=>array("id"=>"magnify","group"=>"Magic360","order"=>"60","default"=>"Yes","label"=>"Magnifier effect","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"magnifier-width"=>array("id"=>"magnifier-width","group"=>"Magic360","order"=>"70","default"=>"80%","label"=>"Magnifier width","description"=>"Magnifier size in % of small image width or fixed size in px","type"=>"text","scope"=>"tool"),"magnifier-shape"=>array("id"=>"magnifier-shape","group"=>"Magic360","order"=>"71","default"=>"inner","label"=>"Shape of magnifying glass","type"=>"array","subType"=>"radio","values"=>array("inner","circle","square"),"scope"=>"tool"),"fullscreen"=>array("id"=>"fullscreen","group"=>"Magic360","order"=>"72","default"=>"Yes","label"=>"Allow full-screen mode","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"spin"=>array("id"=>"spin","group"=>"Magic360","order"=>"110","default"=>"drag","label"=>"Spin","description"=>"Method for spinning the image","type"=>"array","subType"=>"select","values"=>array("drag","hover"),"scope"=>"tool"),"autospin-direction"=>array("id"=>"autospin-direction","group"=>"Magic360","order"=>"111","default"=>"clockwise","label"=>"Direction of auto-spin","type"=>"array","subType"=>"radio","values"=>array("clockwise","anticlockwise","alternate-clockwise","alternate-anticlockwise"),"scope"=>"tool"),"speed"=>array("id"=>"speed","group"=>"Magic360","order"=>"120","default"=>"50","label"=>"Speed","description"=>"Speed of spin (1 - 100)","type"=>"num","scope"=>"tool"),"mousewheel-step"=>array("id"=>"mousewheel-step","group"=>"Magic360","order"=>"121","default"=>"1","label"=>"Mousewheel step","description"=>"Number of frames to spin on mousewheel","type"=>"num","scope"=>"tool"),"autospin-speed"=>array("id"=>"autospin-speed","group"=>"Magic360","order"=>"122","default"=>"3600","label"=>"Speed of auto-spin","description"=>"Choose speed of auto-spin","type"=>"num","scope"=>"tool"),"smoothing"=>array("id"=>"smoothing","group"=>"Magic360","order"=>"130","default"=>"Yes","label"=>"Smoothing","description"=>"Smoothly stop the image spinning","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"autospin"=>array("id"=>"autospin","group"=>"Magic360","order"=>"140","default"=>"once","label"=>"Duration of automatic spin","type"=>"array","subType"=>"select","values"=>array("once","twice","infinite","off"),"scope"=>"tool"),"autospin-start"=>array("id"=>"autospin-start","group"=>"Magic360","order"=>"150","default"=>"load,hover","label"=>"Autospin starts on","description"=>"Start automatic spin on page load, click or hover","type"=>"array","subType"=>"select","values"=>array("load","hover","click","load,hover","load,click"),"scope"=>"tool"),"autospin-stop"=>array("id"=>"autospin-stop","group"=>"Magic360","order"=>"160","default"=>"click","label"=>"Autospin stops on ","description"=>"Stop automatic spin on click or hover","type"=>"array","subType"=>"select","values"=>array("click","hover","never"),"scope"=>"tool"),"initialize-on"=>array("id"=>"initialize-on","group"=>"Magic360","order"=>"170","default"=>"load","label"=>"Initialization","description"=>"When to initialize Magic360&#8482; (download images).","type"=>"array","subType"=>"select","values"=>array("load","hover","click"),"scope"=>"tool"),"start-column"=>array("id"=>"start-column","group"=>"Magic360","order"=>"220","default"=>"1","label"=>"Start column","description"=>"Column from which to start spin. auto means to start from the middle","type"=>"num","scope"=>"tool"),"start-row"=>array("id"=>"start-row","group"=>"Magic360","order"=>"230","default"=>"auto","label"=>"Start row","description"=>"Row from which to start spin. auto means to start from the middle","type"=>"num","scope"=>"tool"),"loop-column"=>array("id"=>"loop-column","group"=>"Magic360","order"=>"240","default"=>"Yes","label"=>"Loop column","description"=>"Continue spin after the last image on X-axis","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"loop-row"=>array("id"=>"loop-row","group"=>"Magic360","order"=>"250","default"=>"No","label"=>"Loop row","description"=>"Continue spin after the last image on Y-axis","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"reverse-column"=>array("id"=>"reverse-column","group"=>"Magic360","order"=>"260","default"=>"No","label"=>"Reverse rotation on X-axis","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"reverse-row"=>array("id"=>"reverse-row","group"=>"Magic360","order"=>"270","default"=>"No","label"=>"Reverse rotation on Y-axis","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"column-increment"=>array("id"=>"column-increment","group"=>"Magic360","order"=>"280","default"=>"1","label"=>"Column increment","description"=>"Load only every second (2) or third (3) column so that spins load faster","type"=>"num","scope"=>"tool"),"row-increment"=>array("id"=>"row-increment","group"=>"Magic360","order"=>"290","default"=>"1","label"=>"Row increment","description"=>"Load only every second (2) or third (3) row so that spins load faster","type"=>"num","scope"=>"tool"),"thumb-max-width"=>array("id"=>"thumb-max-width","group"=>"Positioning and Geometry","order"=>"10","default"=>"250","label"=>"Maximum width of thumbnail (in pixels)","type"=>"num"),"thumb-max-height"=>array("id"=>"thumb-max-height","group"=>"Positioning and Geometry","order"=>"11","default"=>"250","label"=>"Maximum height of thumbnail (in pixels)","type"=>"num"),"square-images"=>array("id"=>"square-images","group"=>"Positioning and Geometry","order"=>"40","default"=>"No","label"=>"Always create square images","description"=>"","type"=>"array","subType"=>"radio","values"=>array("Yes","No")),"icon"=>array("id"=>"icon","group"=>"Miscellaneous","order"=>"10","default"=>"media/magictoolbox/magic360/360icon.png","label"=>"Icon for thumbnail","description"=>"Relative for site base path.","type"=>"text","scope"=>"profile"),"include-headers-on-all-pages"=>array("id"=>"include-headers-on-all-pages","group"=>"Miscellaneous","order"=>"21","default"=>"No","label"=>"Include headers on all pages","description"=>"To be able to apply an effect on any page","type"=>"array","subType"=>"radio","values"=>array("Yes","No")),"show-message"=>array("id"=>"show-message","group"=>"Miscellaneous","order"=>"150","default"=>"Yes","label"=>"Show message under image?","type"=>"array","subType"=>"radio","values"=>array("Yes","No")),"message"=>array("id"=>"message","group"=>"Miscellaneous","order"=>"160","default"=>"Drag image to spin","label"=>"Message under images","type"=>"text"),"loading-text"=>array("id"=>"loading-text","group"=>"Miscellaneous","order"=>"258","default"=>"Loading...","label"=>"Loading text","description"=>"Text displayed while images are loading.","type"=>"text"),"fullscreen-loading-text"=>array("id"=>"fullscreen-loading-text","group"=>"Miscellaneous","order"=>"258","default"=>"Loading large spin...","label"=>"Fullscreen loading text","description"=>"Text shown while full-screen images are loading.","type"=>"text"),"hint"=>array("id"=>"hint","group"=>"Miscellaneous","order"=>"259","default"=>"Yes","label"=>"Show hint message","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"hint-text"=>array("id"=>"hint-text","group"=>"Miscellaneous","order"=>"260","default"=>"Drag to spin","label"=>"Text of the hint on desktop","type"=>"text"),"mobile-hint-text"=>array("id"=>"mobile-hint-text","group"=>"Miscellaneous","order"=>"261","default"=>"Swipe to spin","label"=>"Text of the hint on iOS/Android devices","type"=>"text"));
            $this->params->appendParams($params);
        }

    }

}

?>