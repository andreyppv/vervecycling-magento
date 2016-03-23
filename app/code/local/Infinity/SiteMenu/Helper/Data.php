<?php

/**
 * Infinity Base
 *
 * @category    Infinity
 * @package     Infinity_Base
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Jason.z, Bruce.z
 */
class Infinity_SiteMenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $menu_level = 3;
    public function getWPMenu($wpMenuKey)
    {
        if(!$wpMenuKey)
        {
            return;
        }    
        $WPmenu = array();
 
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $termTaxonomyTable = $resource->getTableName('wp_term_taxonomy'); 
        $termsTable = $resource->getTableName('wp_terms'); 
        $termRelationshipsTable = $resource->getTableName('wp_term_relationships');
        $postsTable = $resource->getTableName('wp_posts');
        $postmetaTable = $resource->getTableName('wp_postmeta');
        
        $query = $read->query("SELECT t.term_id FROM ".$termTaxonomyTable." as tax 
                            LEFT JOIN ".$termsTable." as t ON tax.term_id = t.term_id 
                            WHERE taxonomy = 'nav_menu' and name like '%".$wpMenuKey."%'");

        while ($row = $query->fetch())
        {
                $termId = $row['term_id'];
                break;
        };
        
        if(!isset($termId))
        {
            return;
        }
        
        $query2 = $read->query("SELECT  
                                       p.ID as id,
                                       p.post_title as title,   
                                       m.meta_value, 
                                       pp.meta_value as menu_parent,
                                       pl.meta_value as _menu_item_object_id,
                                       pm.meta_value as _menu_item_object,
                                       pc.meta_value as _menu_item_classes,
                                       n.post_type,
                                       n.guid as url,
                                       n.post_title as page_title,
                                       n.post_name
                    FROM ".$termRelationshipsTable." as txr 
                    INNER JOIN ".$postsTable." as p ON txr.object_id = p.ID 
                    LEFT JOIN ".$postmetaTable." as m ON p.ID = m.post_id 
                    LEFT JOIN ".$postmetaTable." as pl ON p.ID = pl.post_id AND pl.meta_key = '_menu_item_object_id' 
                    LEFT JOIN ".$postmetaTable." as pm ON p.ID = pm.post_id AND pm.meta_key = '_menu_item_object' 
                    LEFT JOIN ".$postmetaTable." as pp ON p.ID = pp.post_id AND pp.meta_key = '_menu_item_menu_item_parent' 
                    LEFT JOIN ".$postmetaTable." as pc ON p.ID = pc.post_id AND pc.meta_key = '_menu_item_classes' 
                    LEFT JOIN ".$postsTable." as n ON pl.meta_value = n.ID 
                    WHERE txr.term_taxonomy_id = '".$termId."' AND p.post_status='publish' 
                        AND p.post_type = 'nav_menu_item' AND m.meta_key = '_menu_item_url' 
                    ORDER BY p.menu_order");

        //$startTagString = '<!--:en-->';
        //$endTagString = '<!--:-->';

        //$startTagString = '[:en]';
        //$endTagString = '[:';
        $children = array();
        
        while ($menuRow = $query2->fetch())
        {
            if(empty($menuRow['title']))
            {
                $menuRow['title'] = $menuRow['page_title'];
            }    
            
            if(strpos($menuRow['title'],'[:en]') !== false)
            {
                $startTagString = '[:en]';
                $endTagString = '[:';
            }
            else
            {
                $startTagString = '<!--:en-->';
                $endTagString = '<!--:-->';
            }
            
            if($start = strpos($menuRow['title'],$startTagString) !== false)
            {
                $end = strpos($menuRow['title'],$endTagString, $start);
                $title = substr($menuRow['title'],$start+strlen($startTagString)-1, $end-$start-strlen($startTagString)+1);
                $menuRow['title'] = $this->__($title);
            }
            
            
            if($menuRow['post_type'] == 'nav_menu_item')
            {
                $menuRow['url'] = $menuRow['meta_value'];
            }    

            if($menuRow['_menu_item_object'] == 'category')
            {
                $this->category_path = '';
                $this->category_title = '';
                $this->getWPParentCategory($menuRow['_menu_item_object_id']);
                
                $menuRow['url'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://':'http://').$_SERVER['HTTP_HOST'].$this->category_path.'/';
                $menuRow['title'] = $this->category_title;
            }    
            if($menuRow['_menu_item_classes'])
            {
                $classData = unserialize($menuRow['_menu_item_classes']);
                $menuRow['class'] = $classData[0];
                unset($menuRow['_menu_item_classes']);
            }
            
            if($menuRow['_menu_item_object'] == 'page')
            {
                $menuRow['url'] = (isset($_SERVER['HTTPS']) ? 'https://':'http://').$_SERVER['HTTP_HOST'].'/'.$menuRow['post_name'].'/';
            }
            
            if($menuRow['menu_parent'] > 0)
            {
                $children[$menuRow['menu_parent']][] = $menuRow;
            }    
            
            
            $WPmenu[] = $menuRow;
        };
        
        
        
        $i = 1;
        while($i < $this->menu_level)
        {
            foreach($children as $parentId => &$childsArr)
            {
                foreach($childsArr as &$child)
                {
                    if(array_key_exists($child['id'],$children))
                    {
                        $child['children'] = $children[$child['id']];
                        //unset($children[$child['id']]);
                    }  
                }
            }
            $i++;
        }
        
        foreach($WPmenu as $key=>&$WPmenuItem)
        {
            if($WPmenuItem['menu_parent'] > 0)
            {
                unset($WPmenu[$key]);
            }
            
            $menuItemId = $WPmenu[$key]['id'];
            if(isset($children[$menuItemId]))
            {
                $WPmenuItem['children'] = $children[$menuItemId];
            }
        }
        
        return $WPmenu;
    }
    
    
    public function getWPParentCategory($childCategoryId)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $termTaxonomyTable = $resource->getTableName('wp_term_taxonomy'); 
        $termsTable = $resource->getTableName('wp_terms');
        $query = $read->query("SELECT parent, name, slug FROM ".$termTaxonomyTable." as term_tax 
                  LEFT JOIN ".$termsTable." as terms ON terms.term_id = term_tax.term_id 

                    WHERE `taxonomy` = 'category' AND `terms`.`term_id` = '".$childCategoryId."' LIMIT 1");
        
        while ($termRelationshipsRow = $query->fetch())
        {
            if(empty($this->category_title))
                $this->category_title = $termRelationshipsRow['name'];
            $parentCategoryId = $termRelationshipsRow['parent'];
            $this->category_path = '/'.$termRelationshipsRow['slug'].$this->category_path;
            break;
        }
        
        
        if(isset($parentCategoryId) && $parentCategoryId !==0 )
        {
            $this->getWPParentCategory($parentCategoryId);
        }    
        
    }
    
    public function getWPMenuHtml($wpMenuKey='', $data=null)
    {
        $WPMenu = (!is_null($data)) ? $data: $this->getWPMenu($wpMenuKey);
        $class = (is_null($data)) ? 'sitemenu' : 'sub-menu';
        $html = ' <ul class="'.$class.'">';
        
        foreach($WPMenu as $item)
        {
			$parentClass = ($item['children'])? ' parent':'';
            $html .= '<li id="menu-item-'.$item['id'].'" class="level-1 '.$parentClass. $item['class'].'"><a href="'. $item['url'].'">'.$this->__($item['title']).'</a>';
            if($item['children'])
            {
                $html .= '<span class="move">&nbsp;</span>';
                $html .= $this->getWPMenuHtml('Main Top Menu',$item['children']);
            }    
             $html .= '</li>';
        }    
		
        $html .= '</ul>';
        return $html;
    }
    
}
