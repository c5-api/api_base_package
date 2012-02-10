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
		$final = array();
		foreach(Package::getInstalledHandles() as $handle) {
			$pkg = Package::getByHandle($handle);
			if(is_object($pkg)) {
				$final[$pkg->getPackageID()] = $handle;
			}
		}
		return $final;
	}


	/**
	 * Get Package Info
	 * @route /package/-/:handle
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
	 * List Package Updates
	 * @route /package/updates
	 * @method GET
	 */		
	public function updates() {
		Loader::library('marketplace');
		$mi = Marketplace::getInstance();
		if ($mi->isConnected()) {
			Marketplace::checkPackageUpdates();
		}
		$local = Package::getLocalUpgradeablePackages();
		$remote = Package::getRemotelyUpgradeablePackages();
		$arr = array();
		foreach($local as $pkg) {
			$arr['local'] = array($pkg->getPackageHandle() => array('current' => $pkg->getPackageCurrentlyInstalledVersion(), 'available' => $pkg->getPackageVersion()));
		}
		foreach($remote as $pkg) {
			$arr['remote'] = array($pkg->getPackageHandle() => array('current' => $pkg->getPackageVersion(), 'available' => $pkg->getPackageVersionUpdateAvailable()));
		}
		return $arr;
	}

	/**
	 * Update Package
	 * @route /package/update
	 * @method POST
	 * @errors ERROR_BAD_REQUEST
	 */		
	public function update() {
		$handle = $_POST['handle'];
		if(!$handle) {
			$resp = ApiResponse::getInstance();
			$resp->setError(true);
			$resp->setCode(400);
			$resp->setMessage('ERROR_BAD_REQUEST');
			$resp->send();
		}
		$pkg = self::validatePkg($handle);
		if(in_array($pkg, Package::getRemotelyUpgradeablePackages())) {
			$rpkg = MarketplaceRemoteItem::getByHandle($pkg->getPackageHandle());
			
		}
		$pkg->upgradeCoreData();
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
	
	public function install() {
		$handle = $_POST['handle'];
		if(!$handle) {
			$resp = ApiResponse::getInstance();
			$resp->setError(true);
			$resp->setCode(400);
			$resp->setMessage('ERROR_BAD_REQUEST');
			$resp->send();
		}
		$pkg = Package::getByHandle($handle);
		if(is_object($pkg)) {
			$resp = ApiResponse::getInstance();
			$resp->setError(true);
			$resp->setCode(400);
			$resp->setMessage('ERROR_ALREADY_INSTALLED');
			$resp->send();
		}
		$pkg = self::validatePkg($handle, false);
		$pkg->install();
		$self = new self();
		return $self->info($handle);
	}
	
	private static function validatePkg($handle, $installed = true) {
		if($installed) {
			$pkg = Package::getByHandle($handle);
		} else {
			$pkg = Loader::package($handle);
		}
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