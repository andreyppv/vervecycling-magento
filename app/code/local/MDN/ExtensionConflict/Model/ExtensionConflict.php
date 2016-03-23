<?php
/**
 * 
 *
 */
class MDN_ExtensionConflict_Model_ExtensionConflict  extends Mage_Core_Model_Abstract
{
	private $_rewriteClassesInformation = null;
	
	/**
	 * Constructor
	 *
	 */
	public function _construct()
	{
		parent::_construct();
		$this->_init('ExtensionConflict/ExtensionConflict');
	}
		
	
	/**
	 * Check if we can fix conflict (if there are more than 2 rewrite classes, no solution)
	 *
	 */
	public function canFix()
	{
		$t = explode(',', $this->getec_rewrite_classes());		
		return (count($t) <= 2);
	}
	
	/**
	 * Return rewrite class info
	 *
	 * @return unknown
	 */
	public function getRewriteClassesInformation()
	{
		if ($this->_rewriteClassesInformation == null)
		{
			$this->_rewriteClassesInformation = array();
			$t = explode(',', $this->getec_rewrite_classes());		
			foreach ($t as $class)
			{
				//collect main information
				$class = trim($class);
				$classArray = array();
				$classArray['class'] = $class;
				$classInfo = explode('_', $class);
				$classArray['editor'] = trim($classInfo[0]);
				$classArray['module'] = trim($classInfo[1]);
				
				//collect config.xml file path
				$classArray['config_file_path'] = mage::helper('ExtensionConflict/Extension')->getConfigFilePath($classArray['editor'], $classArray['module']);
				
				//collect class path
				$classArray['class_path'] = mage::helper('ExtensionConflict/Extension')->getClassPath($class);
				
				//collect class declaration
				$classArray['class_declaration'] = mage::helper('ExtensionConflict/Extension')->getClassDeclaration($class);
				
				//collect new class declaration
				$classArray['new_class_declaration'] = 'class '.$class.' extends ';
				
				$this->_rewriteClassesInformation[] = $classArray;
				
			}
		}
		return $this->_rewriteClassesInformation;
	}
	
	public function getClassInformation1()
	{
		$a = $this->getRewriteClassesInformation();
		if (count($a) == 2)
			return $a[0];
		else 
			return null;
	}
	
	public function getClassInformation2()
	{
		$a = $this->getRewriteClassesInformation();
		if (count($a) == 2)
			return $a[1];
		else 
			return null;
	}

}