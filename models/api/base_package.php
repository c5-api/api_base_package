<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Package Class
 * 
 * Used to list, update and uninstall packages
 * @author Michael Krasnow <mnkras@gmail.com>
 */
class ApiBasePackage extends ApiController {
	
	/**
	 * List of Packages
	 * @route /packages
	 * @method GET
	 */
	public function index() {
		return Package::getInstalledHandles();
	}


	/**
	 * Get Package Info
	 * @route /package/:handle
	 * @method GET
	 */	
	public function info($handle) {
		$pkg = self::validatePkg($handle);
		$db = Loader::db();
		$row = $db->getRow('SELECT pkgVersion FROM Packages WHERE pkgHandle = ?', $handle);
		$arr = array();
		$arr['ID'] = $pkg->getPackageID();
		$arr['handle'] = $pkg->getPackageHandle();
		$arr['name'] = $pkg->getPackageName();
		$arr['description'] = $pkg->getPackageDescription();
		$arr['dateInstalled'] = $pkg->getPackageDateInstalled();
		$arr['version'] = $row['pkgVersion'];
		$arr['versionRequired'] = $pkg->getApplicationVersionRequired();
		$arr['changelog'] = $pkg->getChangelogContents();
		//$arr['items'] = $pkg->getPackageItems(); //too much info!
		return $arr;
		
	}

	/**
	 * Update Package
	 * @route /package/update
	 * @method POST
	 * @errors ERROR_BAD_REQUEST
	 */		
	public function update() {
		if(API_REQUEST_METHOD == 'GET') {
			$local = Package::getLocalUpgradeablePackages();
			$remote = Package::getRemotelyUpgradeablePackages();
			$arr = array();
			foreach($local as $pkg) {
				$arr['local'] = array($pkg->getPackageHandle() => array('current' => $pkg->getPackageCurrentlyInstalledVersion(), 'available' => $pkg->getPackageVersion()));
			}
			foreach($remote as $pkg) {
				$arr['remote'] = array($pkg->getPackageHandle() => array('current' => $pkg->getPackageCurrentlyInstalledVersion(), 'available' => $pkg->getPackageVersion()));
			}
			return $arr;
		}
		$handle = $_POST['handle'];
		if(!$handle) {
			$resp = ApiResponse::getInstance();
			$resp->setError(true);
			$resp->setCode(400);
			$resp->setMessage('ERROR_BAD_REQUEST');
			$resp->send();
		}
		$pkg = self::validatePkg($handle);
		$pkg->upgrade();

	}

	/**
	 * Uninstall Package
	 * @route /package/destroy
	 * @method POST
	 * @errors ERROR_BAD_REQUEST
	 */		
	public function destroy() {
		$trash = false;
		$handle = $_POST['handle'];
		if(!$handle) {
			$resp = ApiResponse::getInstance();
			$resp->setError(true);
			$resp->setCode(400);
			$resp->setMessage('ERROR_BAD_REQUEST');
			$resp->send();
		}
		if(isset($_POST['trash'])) {
			$trash = true;
		}
		$pkg = self::validatePkg($handle);
		$pkg->uninstall();
		if($trash) {
			$pkg->backup();
		}
	}
	
	private static function validatePkg($handle) {
		$pkg = Package::getByHandle($handle);
		if(is_object($pkg)) {
			return $pkg;
		}
		$resp = ApiResponse::getInstance();
		$resp->setError(true);
		$resp->setCode(404);
		$resp->setMessage('ERROR_PACKAGE_NOT_FOUND');
		$resp->send();	
	}

}