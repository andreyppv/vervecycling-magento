<?php

class Infinity_SiteMenu_Model_Url extends Mage_Core_Model_Url
{
    protected function getWPUrls()
    {
        $WpUrls = array();
        $urls = Mage::getStoreConfig('sitemenu/settings/wp_links');
        $WpUrlsArray = explode(',',$urls);
        foreach($WpUrlsArray as $wpUrl)
        {
            $WpUrls[] = trim(trim($wpUrl), '/');
        }
        return $WpUrls;
    } 

    
    public function getUrl($routePath = null, $routeParams = null)
    {
        $escapeQuery = false;
 
        
        /**
         * All system params should be unset before we call getRouteUrl
         * this method has condition for adding default controller and action names
         * in case when we have params
         */
        if (isset($routeParams['_fragment'])) {
            $this->setFragment($routeParams['_fragment']);
            unset($routeParams['_fragment']);
        }

        if (isset($routeParams['_escape'])) {
            $escapeQuery = $routeParams['_escape'];
            unset($routeParams['_escape']);
        }

        $query = null;
        if (isset($routeParams['_query'])) {
            $this->purgeQueryParams();
            $query = $routeParams['_query'];
            unset($routeParams['_query']);
        }

        $noSid = null;
        if (isset($routeParams['_nosid'])) {
            $noSid = (bool)$routeParams['_nosid'];
            unset($routeParams['_nosid']);
        }

        $url = $this->getRouteUrl($routePath, $routeParams);
        /**
         * Apply query params, need call after getRouteUrl for rewrite _current values
         */
        if ($query !== null) {
            if (is_string($query)) {
                $this->setQuery($query);
            } elseif (is_array($query)) {
                $this->setQueryParams($query, !empty($routeParams['_current']));
            }
            if ($query === false) {
                $this->setQueryParams(array());
            }
        }

        if ($noSid !== true) {
            //$this->_prepareSessionUrl($url);
        }

        $query = $this->getQuery($escapeQuery);
        if ($query) {
            $mark = (strpos($url, '?') === false) ? '?' : ($escapeQuery ? '&amp;' : '&');
            $url .= $mark . $query;
        }

        if ($this->getFragment()) {
            $url .= '#' . $this->getFragment();
        }

        $wpUrls = $this->getWPUrls();
        if(in_array(trim($this->getRoutePath($routeParams),'/'), $wpUrls))
        {   
            $url = str_replace('store/', '', $url);
        }
        return $this->escape($url);
    }
}

?>
