<?php

class MDN_ExtensionConflict_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	/**
	 * Refresh list
	 *
	 */
	public function RefreshList()
	{
		//truncate table
		Mage::getResourceModel('ExtensionConflict/ExtensionConflict')->TruncateTable();
		
		//retrieve all config.xml
		$tConfigFiles = $this->getConfigFilesList();
		
		//parse all config.xml
		$rewrites = array();
		foreach($tConfigFiles as $configFile)
		{
			$rewrites = $this->getRewriteForFile($configFile, $rewrites);
		}
		
		//insert in database
		foreach($rewrites as $key => $value)
		{
			$t = explode('/', $key);
			$moduleName = $t[0];
			$className = $t[1];
			
			$record = mage::getModel('ExtensionConflict/ExtensionConflict');
			$record->setec_core_module($moduleName);
			$record->setec_core_class($className);
			
			$rewriteClasses = join(', ', $value);
			$record->setec_rewrite_classes($rewriteClasses);
			
			if (count($value) > 1)
				$record->setec_is_conflict(1);
				
			$record->save();
		}
	}

	/**
	 * create an array with all config.xml files
	 *
	 */
	public function getConfigFilesList()
	{
		$retour = array();
		$codePath = Mage::getStoreConfig('system/filesystem/code');
		
		$tmpPath = Mage::app()->getConfig()->getTempVarDir().'/ExtensionConflict/';
		if (!is_dir($tmpPath))
			mkdir($tmpPath);
		
		$locations = array();
		$locations[] = $codePath.'/local/';
		$locations[] = $codePath.'/community/';
		$locations[] = $tmpPath;
		
		foreach ($locations as $location)
		{
			//parse every sub folders (means extension folders)
			$poolDir = opendir($location);
			while($namespaceName = readdir($poolDir))
			{
				if (!$this->directoryIsValid($namespaceName))
					continue;
					
				//parse modules within namespace
				$namespacePath = $location.$namespaceName.'/';
				$namespaceDir = opendir($namespacePath);
				while($moduleName = readdir($namespaceDir))
				{
					if (!$this->directoryIsValid($moduleName))
						continue;
					
					$modulePath = $namespacePath.$moduleName.'/';
					$configXmlPath = $modulePath.'etc/config.xml';
					
					if (file_exists($configXmlPath))
						$retour[] = $configXmlPath;
				}
				closedir($namespaceDir);
			}
			closedir($poolDir);
		}
		
		return $retour;
	}
	
	/**
	 * 
	 *
	 * @param unknown_type $dirName
	 * @return unknown
	 */
	private function directoryIsValid($dirName)
	{
		switch ($dirName) {
			case '.':
			case '..':
			case '':
				return false;
				break;		
			default:
				return true;
				break;
		}
	}
	
	private function manageModule($moduleName)
	{
		switch ($moduleName) {
			case 'global':
				return false;
				break;		
			default:
				return true;
				break;
		}		
	}
	
	/**
	 * Return all rewrites for a config.xml
	 *
	 * @param unknown_type $configFilePath
	 */
	public function getRewriteForFile($configFilePath, $results)
	{
		//load xml
		$xmlcontent = file_get_contents($configFilePath);
		$domDocument = new DOMDocument();
		$domDocument->loadXML($xmlcontent);
		
		foreach($domDocument->documentElement->getElementsByTagName('rewrite') as $markup)
		{
			//parse child nodes
			$moduleName = $markup->parentNode->tagName;
			if ($this->manageModule($moduleName))
			{
				foreach($markup->getElementsByTagName('*') as $childNode)
				{
					//get information
					$className = $childNode->tagName;
					$rewriteClass = $childNode->nodeValue; 
					
					//add to result
					$key = $moduleName.'/'.$className;
					if (!isset($results[$key]))
						$results[$key] = array();
					$results[$key][] = $rewriteClass;
					
				}
			}
		}
		
		return $results;
	}
	

}

?>