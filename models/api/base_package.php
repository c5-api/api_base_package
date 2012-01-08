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
		$db = Loader::db();
		$r = $db->Execute('SELECT pkgHandle FROM Packages');
		$objs = array();
		while($row = $r->FetchRow()) {
			$objs[] = $row;
		}
		return $objs;
	}


	/**
	 * Get Package Info
	 * @route /packages/:handle
	 * @method GET
	 */	
	public function info($handle) {
		$pkg = self::validatePkg($handle);
		unset($pkg->error);
		return $pkg;
		
	}

	/**
	 * Update Package
	 * @route /packages/update
	 * @method POST
	 * @errors ERROR_NOT_FOUND | ERROR_BAD_REQUEST
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

	}

	/**
	 * Uninstall Package
	 * @route /packages/destroy
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