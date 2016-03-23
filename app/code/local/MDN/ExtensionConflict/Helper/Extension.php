<?php

class MDN_ExtensionConflict_Helper_Extension extends Mage_Core_Helper_Abstract
{
	/**
	 * Return config file path
	 *
	 * @param unknown_type $editor
	 * @param unknown_type $module
	 */
	public function getConfigFilePath($editor, $module)
	{
		$moduleInfo = mage::getConfig()->getModuleConfig($editor.'_'.$module);
		$moduleInfo = $moduleInfo->asArray();
		$path = mage::getBaseDir('base').DS.'app'.DS.'code'.DS.$moduleInfo['codePool'].DS.$editor.DS.$module.DS.'etc'.DS.'config.xml';
		return $path;
	}
	
	/**
	 * Return class path
	 *
	 * @param unknown_type $class
	 */
	public function getClassPath($class)
	{
		$classArray = explode('_', $class);
		$editor = trim($classArray[0]);
		$module = trim($classArray[1]);
		
		$moduleInfo = mage::getConfig()->getModuleConfig($editor.'_'.$module);
		$moduleInfo = $moduleInfo->asArray();
		
		$path = mage::getBaseDir('base').DS.'app'.DS.'code'.DS.$moduleInfo['codePool'].DS.str_replace('_', DS, $class).'.php';
		return $path;
		
	}
	
	/**
	 * Return class declaration
	 *
	 * @param unknown_type $class
	 */
	public function getClassDeclaration($class)
	{
		//instantiate class
		$obj = new $class();	
		$ref = new ReflectionObject($obj);
		$parentClass = $ref->getParentClass()->getname();

		$declaration = 'class '.$class.' extends '.$parentClass;
   		
   		return $declaration;
	}
}