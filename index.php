
<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (version_compare(phpversion(), '5.2.0', '<')===true) {
    echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;">
<div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
<h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">
Whoops, it looks like you have an invalid PHP version.</h3></div><p>Magento supports PHP 5.2.0 or newer.
<a href="http://www.magentocommerce.com/install" target="">Find out</a> how to install</a>
 Magento using PHP-CGI as a work-around.</p></div>';
    exit;
}

/**
 * Error reporting
 */
error_reporting(E_ALL);
//error_reporting(E_STRICT);

/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', getcwd());

$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';

if (!file_exists($mageFilename)) {
    if (is_dir('downloader')) {
        header("Location: downloader");
    } else {
        echo $mageFilename." was not found";
    }
    exit;
}

if (file_exists($maintenanceFile)) {
    include_once dirname(__FILE__) . '/errors/503.php';
    exit;
}

require_once $mageFilename;

#Varien_Profiler::enable();

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}

ini_set('display_errors', 1);

umask(0);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';


$currency = (isset($_COOKIE['currency'])) ? $_COOKIE['currency'] : null;


		if($currency == null) {

			//what country are we in?
			 $ip = $_SERVER['REMOTE_ADDR'];

			//first try with first service
			$ipdat = @json_decode(file_get_contents('http://api.db-ip.com/addrinfo?addr=' . $ip . '&api_key=5a73aacd5a5d54ba1ac63967f2b80a4c02b26e18'));


			if(!isset($ipdat->error) && $ipdat != false && !empty($ipdat)) {
				$ccode = $ipdat->country;	
			} 


			if(preg_match('#[0-9]#',$ccode) || empty($ccode) || strlen($ccode) != 2) {   /// first service failed. Use the alternative
				$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
				$ccode = $ipdat->geoplugin_countryCode;
			} 


			if(preg_match('#[0-9]#',$ccode) || empty($ccode) || strlen($ccode) != 2) {
				$ipdat = @json_decode(file_get_contents('http://ipinfo.io/' . $ip . '/json'));
				$ccode = $ipdat->country;
			}


			 $eu_country = array("BE","BG","CZ","DK","DE","EE","IE","EL","ES","FR","HR","IT","CY","LV","LT","LU","HU","MT","NL","AT","PL","PT","RO","SI","SK","FI","SE","GR"); //European Country list


						if(in_array($ccode,$eu_country))
						{
							 $currency = 'EUR';
						}
						elseif($ccode == 'US'){
							 $currency = 'USD';
						}
						elseif($ccode == 'AU')
						{
							 $currency = 'AUD';
						}
						elseif($ccode == 'GB')
						{
							 $currency = 'GBP';
						}
						else {
							$currency = 'USD';
						}

					setcookie("currency", $currency, time()+60*30,'/','.www.vervecycling.com');

		} 

Mage::register('currency', $currency);


if(isset($_COOKIE['store_for_wp'])) {
  $storeCookielang = $_COOKIE['store_for_wp'];
} else {
  $storeCookielang = null;
}

if(!is_null($storeCookielang))
{
    $storeCookieArr = explode('_',$storeCookielang);
    $lang = end($storeCookieArr);
}
else
{
    $lang = 'en';
}

$mageRunType = 'store';

if($currency == 'USD')
{
    $mageRunCode = 'us_' . $lang;
}
elseif($currency == 'AUD')
{
    $mageRunCode = 'au_' . $lang;
}
elseif($currency == 'GBP')
{
    $mageRunCode = 'uk_' . $lang;
}
elseif($currency == 'EUR')
{
    $mageRunCode = 'eu_' . $lang;
}
else
{
    $mageRunCode = 'us_'.$lang;
    $mageRunType = 'store';
}
Mage::run($mageRunCode, $mageRunType);
?>
